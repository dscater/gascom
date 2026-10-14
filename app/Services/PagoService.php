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
        $detalles = [];
        $participantes = [];

        foreach ($datos["pago_detalles"] as $item) {
            $datos_item = [
                "gasto_id" => $item["gasto_id"],
                "monto" => $item["monto"],
                "fecha" => $item["fecha"] ?? null,
            ];

            $detalle = $pago->pago_detalles()->create($datos_item);
            $detalles[$item['_key']] = $detalle->id;
        }

        foreach ($datos["pago_participantes"] as $item) {
            $datos_item = [
                "participante_id" => $item["participante_id"],
            ];

            $participante = $pago->pago_participantes()->create($datos_item);
            $participantes[$item['_key']] = $participante->id;
        }

        $totalesParticipantes = [];

        foreach ($datos["pago_gastos"] as $item) {
            $detalleId = !empty($item['pago_detalle_id'])
                ? (int) $item['pago_detalle_id']
                : ($detalles[$item['_detalle_key'] ?? ''] ?? null);

            $pagoParticipanteId = !empty($item['pago_participante_id'])
                ? (int) $item['pago_participante_id']
                : (
                    $participantes[$item['_participante_key'] ?? ''] ?? null
                );

            if (!$detalleId || !$pagoParticipanteId) {
                throw new \RuntimeException(
                    'No se pudo identificar el detalle o participante de una distribución.'
                );
            }

            // Verificar que ambos registros pertenezcan al pago.
            $detalle = $pago->pago_detalles()
                ->whereKey($detalleId)
                ->firstOrFail();

            $pagoParticipante = $pago->pago_participantes()
                ->whereKey($pagoParticipanteId)
                ->firstOrFail();

            // No confiar en el participante_id enviado por el navegador.
            $participanteId = $pagoParticipante->participante_id;

            $montoPagado = round(
                (float) ($item['monto_pagado'] ?? 0),
                2
            );

            $porcentaje = (float) (
                $item['porcentaje_pago'] ?? 0
            );

            PagoGasto::create([
                'pago_id' => $pago->id,
                'pago_detalle_id' => $detalle->id,
                'pago_participante_id' => $pagoParticipante->id,
                'participante_id' => $participanteId,
                'gasto_id' => $detalle->gasto_id,
                'porcentaje_pago' => $porcentaje,
                'monto_pagado' => $montoPagado,
                'estado' => $item['estado'] ?? 'PENDIENTE',
            ]);

            $totalesParticipantes[$pagoParticipante->id] =
                ($totalesParticipantes[$pagoParticipante->id] ?? 0)
                + $montoPagado;
        }

        foreach ($totalesParticipantes as $id => $total) {
            $pago->pago_participantes()
                ->whereKey($id)
                ->update([
                    'total' => round($total, 2),
                ]);
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

        $pago->update([
            'mes' => $datos["mes"],
            'anio' => $datos["anio"],
            'total' => $datos["total"] ?? 0,
        ]);

        $eliminadosDetalles = $datos['eliminados_detalles'] ?? [];

        $eliminadosParticipantes = $datos['eliminados_participantes'] ?? [];

        // Eliminar primero las distribuciones relacionadas
        // con los detalles o participantes eliminados.
        $distribuciones = PagoGasto::where('pago_id', $pago->id);

        if (!empty($eliminadosDetalles)) {
            $distribuciones->whereIn(
                'pago_detalle_id',
                $eliminadosDetalles
            )->delete();
        }
        $distribuciones = PagoGasto::where('pago_id', $pago->id);

        if (!empty($eliminadosParticipantes)) {
            $distribuciones->whereIn(
                'pago_participante_id',
                $eliminadosParticipantes
            )->delete();
        }

        // 3. Eliminar los detalles solicitados.
        if (!empty($eliminadosDetalles)) {
            $pago->pago_detalles()
                ->whereIn('id', $eliminadosDetalles)
                ->delete();
        }

        // 4. Eliminar los participantes solicitados.
        if (!empty($eliminadosParticipantes)) {
            $pago->pago_participantes()
                ->whereIn('id', $eliminadosParticipantes)
                ->delete();
        }

        // Mapas para resolver los IDs de los registros nuevos.
        $detalles = [];
        $participantes = [];

        // 5. Crear o actualizar detalles.
        foreach ($datos["pago_detalles"] as $item) {
            $detalleId = $item['id'] ?? 0;

            if ($detalleId) {
                $detalle = $pago->pago_detalles()
                    ->whereKey($detalleId)
                    ->firstOrFail();

                $detalle->update([
                    'gasto_id' => $item['gasto_id'],
                    'monto' => $item['monto'] ?? 0,
                    'fecha' => $item['fecha'] ?? null,
                ]);
            } else {
                $detalle = $pago->pago_detalles()->create([
                    'gasto_id' => $item['gasto_id'],
                    'monto' => $item['monto'] ?? 0,
                    'fecha' => $item['fecha'] ?? null,
                ]);
            }

            $detalles[$item['_key']] = $detalle->id;
            $detalles['id_' . $detalle->id] = $detalle->id;
        }

        // 6. Crear o actualizar participantes.
        foreach ($datos["pago_participantes"] as $item) {
            $participanteId = $item['id'] ?? 0;

            if ($participanteId) {
                $participante = $pago->pago_participantes()
                    ->whereKey($participanteId)
                    ->firstOrFail();

                $participante->update([
                    'participante_id' => $item['participante_id'],
                ]);
            } else {
                $participante = $pago->pago_participantes()->create([
                    'participante_id' => $item['participante_id'],
                    'correo_enviado' => 0,
                    'total' => 0,
                    'estado' => 'PENDIENTE',
                ]);
            }

            $participantes[$item['_key']] = $participante->id;
            $participantes['id_' . $participante->id] =
                $participante->id;
        }

        // 7. Sincronizar las distribuciones.
        $idsDistribuciones = [];
        $totalesParticipantes = [];

        foreach ($datos["pago_gastos"] as $item) {
            $detalleId = !empty($item['pago_detalle_id'])
                ? ($detalles['id_' . $item['pago_detalle_id']] ?? null)
                : ($detalles[$item['_detalle_key'] ?? ''] ?? null);

            $pagoParticipanteId = !empty($item['pago_participante_id'])
                ? (
                    $participantes['id_' . $item['pago_participante_id']] ?? null
                )
                : (
                    $participantes[$item['_participante_key'] ?? ''] ?? null
                );

            if (!$detalleId || !$pagoParticipanteId) {
                throw ValidationException::withMessages([
                    'pago_gastos' =>
                    'No se pudo relacionar una distribución con su detalle y participante.',
                ]);
            }

            $detalle = $pago->pago_detalles()
                ->whereKey($detalleId)
                ->firstOrFail();

            $pagoParticipante = $pago->pago_participantes()
                ->whereKey($pagoParticipanteId)
                ->firstOrFail();

            // El participante real se obtiene del registro asociado
            // al pago, no de un ID enviado libremente por el cliente.
            $monto = round(
                (float) ($item['monto_pagado'] ?? 0),
                2
            );

            $distribucion = PagoGasto::updateOrCreate(
                [
                    'pago_id' => $pago->id,
                    'pago_detalle_id' => $detalle->id,
                    'pago_participante_id' => $pagoParticipante->id,
                ],
                [
                    'participante_id' =>
                    $pagoParticipante->participante_id,
                    'gasto_id' => $detalle->gasto_id,
                    'porcentaje_pago' =>
                    $item['porcentaje_pago'] ?? 0,
                    'monto_pagado' => $monto,
                    'estado' => $item['estado'] ?? 'PENDIENTE',
                ]
            );

            $idsDistribuciones[] = $distribucion->id;

            $totalesParticipantes[$pagoParticipante->id] =
                ($totalesParticipantes[$pagoParticipante->id] ?? 0)
                + $monto;
        }

        // 8. Eliminar distribuciones que ya no están en la matriz.
        // Esto evita conservar celdas obsoletas.
        $obsoletas = PagoGasto::where('pago_id', $pago->id);

        if (!empty($idsDistribuciones)) {
            $obsoletas->whereNotIn('id', $idsDistribuciones);
        }

        $obsoletas->delete();

        // 9. Actualizar los totales de los participantes.
        foreach ($pago->pago_participantes as $participante) {
            $participante->update([
                'total' => round(
                    $totalesParticipantes[$participante->id] ?? 0,
                    2
                ),
            ]);
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
