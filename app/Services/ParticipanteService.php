<?php

namespace App\Services;

use App\Models\CampeonatoInscripcion;
use App\Models\PagoParticipante;
use App\Services\HistorialAccionService;
use App\Models\Participante;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Exception;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ParticipanteService
{
    private $modulo = "PARTICIPANTES";

    public function __construct(private  CargarArchivoService $cargarArchivoService, private HistorialAccionService $historialAccionService) {}

    public function listado(
        $campeonato_id = null,
        $sin_inscripcion = false,
        $participante_id = null
    ): Collection {
        $participantes = Participante::select("participantes.*");

        if ($campeonato_id) {
            // Log::debug($sin_inscripcion);
            if ($sin_inscripcion) {
                // Participantes que NO están inscritas en este campeonato
                $participantes->where(function ($query) use ($campeonato_id, $participante_id) {
                    $query->whereDoesntHave("campeonato_inscripcions", function ($q) use ($campeonato_id) {
                        $q->where("campeonato_id", $campeonato_id);
                    });

                    // Si estamos editando, incluir el id
                    if ($participante_id) {
                        $query->orWhere("participantes.id", $participante_id);
                    }
                });
            } else {
                // Participantes que SÍ están inscritas en este campeonato
                $participantes->whereHas("campeonato_inscripcions", function ($query) use ($campeonato_id) {
                    $query->where("campeonato_id", $campeonato_id);
                });
            }
        }

        $participantes = $participantes->get();
        return $participantes;
    }
    /**
     * Lista de participantes paginado con filtros
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
        $participantes = Participante::select("participantes.*");

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $participantes->where("participantes.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $participantes->whereBetween("participantes.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $participantes->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $participantes->orderBy($value[0], $value[1]);
            }
        }


        $participantes = $participantes->paginate($length, ['*'], 'page', $page);
        return $participantes;
    }

    /**
     * Crear participante
     *
     * @param array $datos
     * @return Participante
     */
    public function crear(array $datos): Participante
    {
        $participante = Participante::create([
            "nombre" => mb_strtoupper($datos["nombre"]),
            "correo" => mb_strtolower($datos["correo"]),
            "descripcion" => mb_strtoupper($datos["descripcion"]) ?? NULL,
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UN PARTICIPANTE", $participante);

        return $participante;
    }

    /**
     * Actualizar participante
     *
     * @param array $datos
     * @param Participante $participante
     * @return Participante
     */
    public function actualizar(array $datos, Participante $participante): Participante
    {
        $old_participante = clone $participante;

        $participante->update([
            "nombre" => mb_strtoupper($datos["nombre"]),
            "correo" => mb_strtolower($datos["correo"]),
            "descripcion" => mb_strtoupper($datos["descripcion"]) ?? NULL,
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UN PARTICIPANTE", $old_participante, $participante->withoutRelations());

        return $participante;
    }

    /**
     * Eliminar participante
     *
     * @param Participante $participante
     * @return boolean
     */
    public function eliminar(Participante $participante): bool|Exception
    {
        $old_participante = clone $participante;
        $usos = PagoParticipante::where("participante_id", $participante->id)->count();
        if ($usos > 0) {
            throw new Exception("No se puede eliminar este tipo de documento porque está siendo utilizado por $usos pagos.");
        }

        $participante->delete();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UN PARTICIPANTE", $old_participante, $participante);

        return true;
    }

    public function cargarLogo(Participante $participante, UploadedFile $logo): void
    {
        if ($participante->logo) {
            \File::delete(public_path("imgs/participantes/" . $participante->logo));
        }

        $nombre = $participante->id . time();
        $participante->logo = $this->cargarArchivoService->cargarArchivo($logo, public_path("imgs/participantes"), $nombre);
        $participante->save();
    }
}
