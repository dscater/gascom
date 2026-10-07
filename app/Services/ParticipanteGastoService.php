<?php

namespace App\Services;

use App\Models\CampeonatoInscripcion;
use App\Services\HistorialAccionService;
use App\Models\ParticipanteGasto;
use App\Models\PagoDetalle;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Exception;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ParticipanteGastoService
{
    private $modulo = "PARTICIPANTE GASTOS";

    public function __construct(private  CargarArchivoService $cargarArchivoService, private HistorialAccionService $historialAccionService) {}

    public function listado(
        $campeonato_id = null,
        $sin_inscripcion = false,
        $participante_gasto_id = null
    ): Collection {
        $participante_gastos = ParticipanteGasto::select("participante_gastos.*");

        if ($campeonato_id) {
            // Log::debug($sin_inscripcion);
            if ($sin_inscripcion) {
                // ParticipanteGastos que NO están inscritas en este campeonato
                $participante_gastos->where(function ($query) use ($campeonato_id, $participante_gasto_id) {
                    $query->whereDoesntHave("campeonato_inscripcions", function ($q) use ($campeonato_id) {
                        $q->where("campeonato_id", $campeonato_id);
                    });

                    // Si estamos editando, incluir el id
                    if ($participante_gasto_id) {
                        $query->orWhere("participante_gastos.id", $participante_gasto_id);
                    }
                });
            } else {
                // ParticipanteGastos que SÍ están inscritas en este campeonato
                $participante_gastos->whereHas("campeonato_inscripcions", function ($query) use ($campeonato_id) {
                    $query->where("campeonato_id", $campeonato_id);
                });
            }
        }

        $participante_gastos = $participante_gastos->get();
        return $participante_gastos;
    }
    /**
     * Lista de participante_gastos paginado con filtros
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
        $participante_gastos = ParticipanteGasto::with(["gasto", "participante"])
            ->select("participante_gastos.*")
            ->join("participantes", "participantes.id", "=", "participante_gastos.participante_id")
            ->join("gastos", "gastos.id", "=", "participante_gastos.gasto_id");

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $participante_gastos->where("participante_gastos.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $participante_gastos->whereBetween("participante_gastos.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $participante_gastos->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $participante_gastos->orderBy($value[0], $value[1]);
            }
        }


        $participante_gastos = $participante_gastos->paginate($length, ['*'], 'page', $page);
        return $participante_gastos;
    }

    /**
     * Crear participante_gasto
     *
     * @param array $datos
     * @return ParticipanteGasto
     */
    public function crear(array $datos): ParticipanteGasto
    {
        $existe = ParticipanteGasto::where("participante_id", $datos["participante_id"])
            ->where("gasto_id", $datos["gasto_id"])
            ->get()->first();

        if ($existe) throw new Exception("El participante ya tiene un % asignado a ese gasto");

        $participante_gasto = ParticipanteGasto::create([
            "participante_id" => $datos["participante_id"],
            "gasto_id" => $datos["gasto_id"],
            "porcentaje" => $datos["porcentaje"],
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UN PARTICIPANTE GASTO", $participante_gasto);

        return $participante_gasto;
    }

    /**
     * Actualizar participante_gasto
     *
     * @param array $datos
     * @param ParticipanteGasto $participante_gasto
     * @return ParticipanteGasto
     */
    public function actualizar(array $datos, ParticipanteGasto $participante_gasto): ParticipanteGasto
    {

        $existe = ParticipanteGasto::where("participante_id", $datos["participante_id"])
            ->where("gasto_id", $datos["gasto_id"])
            ->where("id", "!=", $participante_gasto->id)
            ->get()->first();

        if ($existe) throw new Exception("El participante ya tiene un % asignado a ese gasto");

        $old_participante_gasto = clone $participante_gasto;

        $participante_gasto->update([
            "participante_id" => $datos["participante_id"],
            "gasto_id" => $datos["gasto_id"],
            "porcentaje" => $datos["porcentaje"],
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UN PARTICIPANTE GASTO", $old_participante_gasto, $participante_gasto->withoutRelations());

        return $participante_gasto;
    }

    /**
     * Eliminar participante_gasto
     *
     * @param ParticipanteGasto $participante_gasto
     * @return boolean
     */
    public function eliminar(ParticipanteGasto $participante_gasto): bool|Exception
    {
        $old_participante_gasto = clone $participante_gasto;
        $participante_gasto->delete();
        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UN PARTICIPANTE GASTO", $old_participante_gasto, $participante_gasto);

        return true;
    }

    public function cargarLogo(ParticipanteGasto $participante_gasto, UploadedFile $logo): void
    {
        if ($participante_gasto->logo) {
            \File::delete(public_path("imgs/participante_gastos/" . $participante_gasto->logo));
        }

        $nombre = $participante_gasto->id . time();
        $participante_gasto->logo = $this->cargarArchivoService->cargarArchivo($logo, public_path("imgs/participante_gastos"), $nombre);
        $participante_gasto->save();
    }
}
