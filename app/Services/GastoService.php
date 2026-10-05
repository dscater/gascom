<?php

namespace App\Services;

use App\Models\CampeonatoInscripcion;
use App\Services\HistorialAccionService;
use App\Models\Gasto;
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

class GastoService
{
    private $modulo = "GASTOS";

    public function __construct(private  CargarArchivoService $cargarArchivoService, private HistorialAccionService $historialAccionService) {}

    public function listado(
        $campeonato_id = null,
        $sin_inscripcion = false,
        $gasto_id = null
    ): Collection {
        $gastos = Gasto::select("gastos.*");

        if ($campeonato_id) {
            // Log::debug($sin_inscripcion);
            if ($sin_inscripcion) {
                // Gastos que NO están inscritas en este campeonato
                $gastos->where(function ($query) use ($campeonato_id, $gasto_id) {
                    $query->whereDoesntHave("campeonato_inscripcions", function ($q) use ($campeonato_id) {
                        $q->where("campeonato_id", $campeonato_id);
                    });

                    // Si estamos editando, incluir el id
                    if ($gasto_id) {
                        $query->orWhere("gastos.id", $gasto_id);
                    }
                });
            } else {
                // Gastos que SÍ están inscritas en este campeonato
                $gastos->whereHas("campeonato_inscripcions", function ($query) use ($campeonato_id) {
                    $query->where("campeonato_id", $campeonato_id);
                });
            }
        }

        $gastos = $gastos->get();
        return $gastos;
    }
    /**
     * Lista de gastos paginado con filtros
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
        $gastos = Gasto::select("gastos.*");

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $gastos->where("gastos.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $gastos->whereBetween("gastos.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $gastos->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $gastos->orderBy($value[0], $value[1]);
            }
        }


        $gastos = $gastos->paginate($length, ['*'], 'page', $page);
        return $gastos;
    }

    /**
     * Crear gasto
     *
     * @param array $datos
     * @return Gasto
     */
    public function crear(array $datos): Gasto
    {
        $gasto = Gasto::create([
            "nombre" => mb_strtoupper($datos["nombre"]),
            "descripcion" => mb_strtoupper($datos["descripcion"]) ?? NULL,
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UN GASTO", $gasto);

        return $gasto;
    }

    /**
     * Actualizar gasto
     *
     * @param array $datos
     * @param Gasto $gasto
     * @return Gasto
     */
    public function actualizar(array $datos, Gasto $gasto): Gasto
    {
        $old_gasto = clone $gasto;

        $gasto->update([
            "nombre" => mb_strtoupper($datos["nombre"]),
            "descripcion" => mb_strtoupper($datos["descripcion"]) ?? NULL,
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UN GASTO", $old_gasto, $gasto->withoutRelations());

        return $gasto;
    }

    /**
     * Eliminar gasto
     *
     * @param Gasto $gasto
     * @return boolean
     */
    public function eliminar(Gasto $gasto): bool|Exception
    {
        $old_gasto = clone $gasto;
        $usos = PagoDetalle::where("gasto_id", $gasto->id)->count();
        if ($usos > 0) {
            throw new Exception("No se puede eliminar este tipo de documento porque está siendo utilizado por $usos pagos.");
        }

        $gasto->delete();

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "ELIMINACIÓN", "ELIMINÓ UN GASTO", $old_gasto, $gasto);

        return true;
    }

    public function cargarLogo(Gasto $gasto, UploadedFile $logo): void
    {
        if ($gasto->logo) {
            \File::delete(public_path("imgs/gastos/" . $gasto->logo));
        }

        $nombre = $gasto->id . time();
        $gasto->logo = $this->cargarArchivoService->cargarArchivo($logo, public_path("imgs/gastos"), $nombre);
        $gasto->save();
    }
}
