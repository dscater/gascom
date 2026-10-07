<?php

namespace App\Services;

use App\Models\CampeonatoInscripcion;
use App\Models\PagoPago;
use App\Services\HistorialAccionService;
use App\Models\Pago;
use App\Models\PagoDetalle;
use App\Models\PagoGasto;
use App\Models\PagoParticipante;
use App\Models\ParticipanteGasto;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Exception;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PagoService
{
    private $modulo = "PAGOS";

    public function __construct(private  CargarArchivoService $cargarArchivoService, private HistorialAccionService $historialAccionService, private EnviarCorreoService $enviar_correo_service) {}

    public function listado(
        $campeonato_id = null,
        $sin_inscripcion = false,
        $pago_id = null
    ): Collection {
        $pagos = Pago::select("pagos.*");

        if ($campeonato_id) {
            // Log::debug($sin_inscripcion);
            if ($sin_inscripcion) {
                // Pagos que NO están inscritas en este campeonato
                $pagos->where(function ($query) use ($campeonato_id, $pago_id) {
                    $query->whereDoesntHave("campeonato_inscripcions", function ($q) use ($campeonato_id) {
                        $q->where("campeonato_id", $campeonato_id);
                    });

                    // Si estamos editando, incluir el id
                    if ($pago_id) {
                        $query->orWhere("pagos.id", $pago_id);
                    }
                });
            } else {
                // Pagos que SÍ están inscritas en este campeonato
                $pagos->whereHas("campeonato_inscripcions", function ($query) use ($campeonato_id) {
                    $query->where("campeonato_id", $campeonato_id);
                });
            }
        }

        $pagos = $pagos->get();
        return $pagos;
    }
    /**
     * Lista de pagos paginado con filtros
     *
     * @param integer $length
     * @param integer $page
     * @param string $search
     * @param array $columnsSerachLike
     * @param array $columnsFilter
     * @return LengthAwarePaginator
     */
    public function listadoPaginado(int $length, int $page, string $search, array $columnsSerachLike = [], array $columnsFilter = [], array $columnsBetweenFilter = [], array $orderBy = []): LengthAwarePaginator
    {
        $pagos = Pago::select("pagos.*");

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $pagos->where("pagos.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $pagos->whereBetween("pagos.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $pagos->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $pagos->orderBy($value[0], $value[1]);
            }
        }


        $pagos = $pagos->paginate($length, ['*'], 'page', $page);
        return $pagos;
    }

    /**
     * Crear pago
     *
     * @param array $datos
     * @return Pago
     */
    public function crear(array $datos): Pago
    {
        $pago = Pago::create([
            "mes" => $datos["mes"],
            "anio" => $datos["anio"],
            "total" => $datos["total"],
            "fecha_registro" => date("Y-m-d")
        ]);

        foreach ($datos["pago_detalles"] as $item) {
            $datos_item = [
                "gasto_id" => $item["gasto_id"],
                "monto" => $item["monto"],
                "fecha" => $item["fecha"] ?? null,
            ];

            $pago->pago_detalles()->create($datos_item);
        }

        foreach ($datos["pago_participantes"] as $item) {
            $datos_item = [
                "participante_id" => $item["participante_id"],
            ];

            $pago->pago_participantes()->create($datos_item);
        }

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UN PAGO", $pago, null, ["pago_detalles", "pago_participantes"]);

        return $pago;
    }

    public function distribuir(Pago $pago): Pago
    {
        $old_pago = clone $pago;

        $total_participantes = $pago->pago_participantes->count();

        // porcentaje %
        $porcentaje = 100 / $total_participantes;
        $porcentaje = round($porcentaje, 8);
        foreach ($pago->pago_participantes as $item_participante) {
            $total_pagado_participante = 0;
            foreach ($pago->pago_detalles as $item_detalle) {
                $existe = PagoGasto::where("pago_id", $pago->id)
                    ->where("participante_id", $item_participante->participante_id)
                    ->where("pago_participante_id", $item_participante->id)
                    ->where("pago_detalle_id", $item_detalle->gasto_id)
                    ->where("gasto_id", $item_detalle->gasto_id)
                    ->get()->first();

                if ($existe) continue;
                // monto_pagado 
                $participante_gasto = ParticipanteGasto::where("participante_id", $item_participante->participante_id)
                    ->where("gasto_id", $item_detalle->gasto_id)
                    ->get()->first();
                if ($participante_gasto) {
                    // con porcentaje asignado
                    $porcentaje = $participante_gasto->porcentaje;
                    $monto_pagado = (float)$item_detalle->monto * ($porcentaje / 100);
                    $monto_pagado = round($monto_pagado, 2);
                } else {
                    // sin porcentaje asignado
                    $monto_pagado = (float)$item_detalle->monto * ($porcentaje / 100);
                    $monto_pagado = round($monto_pagado, 2);
                }

                $pago->pago_gastos()->create([
                    "pago_detalle_id" => $item_detalle->id,
                    "participante_id" => $item_participante->participante_id,
                    "pago_participante_id" => $item_participante->id,
                    "gasto_id" => $item_detalle->gasto_id,
                    "porcentaje_pago" => $porcentaje,
                    "monto_pagado" => $monto_pagado,
                ]);

                $total_pagado_participante += (float)$monto_pagado;
            }
            $item_participante->total = $total_pagado_participante;
            $item_participante->save();
        }

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "DISTRIBUYO UN PAGO", $old_pago, $pago->withoutRelations());

        return $pago;
    }

    /**
     * Actualizar pago
     *
     * @param array $datos
     * @param Pago $pago
     * @return Pago
     */
    public function actualizar(array $datos, Pago $pago): Pago
    {
        $old_pago = clone $pago;

        $old_pago = clone $pago;

        $total_participantes = $pago->pago_participantes->count();

        // porcentaje %
        $porcentaje = 100 / $total_participantes;
        $porcentaje = round($porcentaje, 8);
        foreach ($pago->pago_participantes as $item_participante) {
            $total_pagado_participante = 0;
            foreach ($pago->pago_detalles as $item_detalle) {
                $existe = PagoGasto::where("pago_id", $pago->id)
                    ->where("participante_id", $item_participante->participante_id)
                    ->where("pago_participante_id", $item_participante->id)
                    ->where("pago_detalle_id", $item_detalle->gasto_id)
                    ->where("gasto_id", $item_detalle->gasto_id)
                    ->get()->first();

                // monto_pagado 
                $participante_gasto = ParticipanteGasto::where("participante_id", $item_participante->participante_id)
                    ->where("gasto_id", $item_detalle->gasto_id)
                    ->get()->first();
                if ($participante_gasto) {
                    // con porcentaje asignado
                    $porcentaje = $participante_gasto->porcentaje;
                    $monto_pagado = (float)$item_detalle->monto * ($porcentaje / 100);
                    $monto_pagado = round($monto_pagado, 2);
                } else {
                    // sin porcentaje asignado
                    $monto_pagado = (float)$item_detalle->monto * ($porcentaje / 100);
                    $monto_pagado = round($monto_pagado, 2);
                }

                if ($existe) {
                    $existe->update([
                        "pago_detalle_id" => $item_detalle->id,
                        "participante_id" => $item_participante->participante_id,
                        "pago_participante_id" => $item_participante->id,
                        "gasto_id" => $item_detalle->gasto_id,
                        "porcentaje_pago" => $porcentaje,
                        "monto_pagado" => $monto_pagado,
                    ]);
                } else {
                    $pago->pago_gastos()->create([
                        "pago_detalle_id" => $item_detalle->id,
                        "participante_id" => $item_participante->participante_id,
                        "pago_participante_id" => $item_participante->id,
                        "gasto_id" => $item_detalle->gasto_id,
                        "porcentaje_pago" => $porcentaje,
                        "monto_pagado" => $monto_pagado,
                    ]);
                }
                $total_pagado_participante += (float)$monto_pagado;
            }
            $item_participante->total = $total_pagado_participante;
            $item_participante->save();
        }

        if (isset($eliminados_detalles)) {
            foreach ($eliminados_detalles as $item_id) {
                $pago_detalle = PagoDetalle::findOrFail($item_id);
                $pago_detalle->delete();
            }
        }

        if (isset($eliminados_participantes)) {
            foreach ($eliminados_participantes as $item_id) {
                $pago_participante = PagoParticipante::findOrFail($item_id);
                $pago_participante->delete();
            }
        }

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UN PAGO", $old_pago, $pago->withoutRelations());

        return $pago;
    }

    public function guardar_distribuir(array $datos, Pago $pago)
    {
        $old_pago = clone $pago;

        $total_participantes = [];
        foreach ($datos["matrizGastos"] as $item_detalle) {
            foreach ($item_detalle["participantes"] as $item_participante) {
                $pago_gasto = PagoGasto::findOrFail($item_participante["gasto"]["id"]);
                $pago_gasto->update([
                    "porcentaje_pago" => $item_participante["gasto"]["porcentaje_pago"],
                    "monto_pagado" => $item_participante["gasto"]["monto_pagado"],
                ]);

                if (!isset($total_participantes[$item_participante["participante"]["participante_id"]])) {
                    $total_participantes[$item_participante["participante"]["participante_id"]] = 0;
                }
                $total_participantes[$item_participante["participante"]["participante_id"]] += (float)$item_participante["gasto"]["monto_pagado"];
            }
        }

        foreach ($pago->pago_participantes as $participante) {
            $participante->total = $total_participantes[$participante->id];
            $participante->save();
        }

        $this->enviar_correo_service->mailDistribucionPagos($pago);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "GUARDO Y DISTRIBUYÓ UN PAGO", $old_pago, $pago->withoutRelations());

        return $pago;
    }

    /**
     * Eliminar pago
     *
     * @param Pago $pago
     * @return boolean
     */
    public function eliminar(Pago $pago): bool|Exception
    {
        $old_pago = clone $pago;
        $usos = PagoPago::where("pago_id", $pago->id)->count();
        if ($usos > 0) {
            throw new Exception("No se puede eliminar este tipo de documento porque está siendo utilizado por $usos pagos.");
        }

        $pago->delete();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UN PAGO", $old_pago, $pago);

        return true;
    }

    public function cargarLogo(Pago $pago, UploadedFile $logo): void
    {
        if ($pago->logo) {
            \File::delete(public_path("imgs/pagos/" . $pago->logo));
        }

        $nombre = $pago->id . time();
        $pago->logo = $this->cargarArchivoService->cargarArchivo($logo, public_path("imgs/pagos"), $nombre);
        $pago->save();
    }
}
