<?php

namespace App\Services;

use App\Models\CampeonatoInscripcion;
use App\Models\PagoPago;
use App\Services\HistorialAccionService;
use App\Models\Pago;
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

    public function __construct(private  CargarArchivoService $cargarArchivoService, private HistorialAccionService $historialAccionService) {}

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
            "nombre" => mb_strtoupper($datos["nombre"]),
            "descripcion" => mb_strtoupper($datos["descripcion"]) ?? NULL,
        ]);

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "CREACIÓN", "REGISTRO UN PAGO", $pago);

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

        // cargar logo
        if (isset($datos["logo"]) && !is_string($datos["logo"])) {
            $this->cargarLogo($pago, $datos["logo"]);
        }

        // registrar accion
        $this->historialAccionService->registrarAccion($this->modulo, "MODIFICACIÓN", "ACTUALIZÓ UN PAGO", $old_pago, $pago->withoutRelations());

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
