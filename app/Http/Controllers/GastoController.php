<?php

namespace App\Http\Controllers;

use App\Http\Requests\GastoStoreRequest;
use App\Http\Requests\GastoUpdateRequest;
use App\Models\Gasto;
use App\Models\User;
use App\Services\GastoService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as ResponseInertia;

class GastoController extends Controller
{
    public function __construct(private GastoService $gastoService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): ResponseInertia
    {
        return Inertia::render("Admin/Gastos/Index");
    }

    /**
     * Listado de gastos sin ids: 1 y 2
     *
     * @return JsonResponse
     */
    public function listado(Request $request): JsonResponse
    {
        return response()->JSON([
            "gastos" => $this->gastoService->listado(
                $request->input("campeonato_id", null),
                $request->input("sin_inscripcion", false),
                $request->input("gasto_id", null),
            )
        ]);
    }

    public function paginado(Request $request)
    {
        $perPage = $request->perPage;
        $page = (int)($request->input("page", 1));
        $search = (string)$request->input("search", "");
        $orderBy = $request->orderBy;
        $orderAsc = $request->orderAsc;

        $columnsSerachLike = [
            "nombre",
            "descripcion",
        ];
        $columnsFilter = [];
        $columnsBetweenFilter = [];
        $arrayOrderBy = [];
        if ($orderBy && $orderAsc) {
            $arrayOrderBy = [
                [$orderBy, $orderAsc]
            ];
        }

        $gastos = $this->gastoService->listadoPaginado($perPage, $page, $search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $arrayOrderBy);
        return response()->JSON([
            "data" => $gastos->items(),
            "total" => $gastos->total(),
            "lastPage" => $gastos->lastPage()
        ]);
    }

    /**
     * Registrar un nuevo gasto
     *
     * @param GastoStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(GastoStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el Gasto
            $this->gastoService->crear($request->validated());
            DB::commit();
            return redirect()->route("gastos.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un gasto
     *
     * @param Gasto $gasto
     * @return JsonResponse
     */
    public function show(Gasto $gasto): JsonResponse
    {
        return response()->JSON($gasto);
    }

    public function update(Gasto $gasto, GastoUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar gasto
            $this->gastoService->actualizar($request->validated(), $gasto);
            DB::commit();
            return redirect()->route("gastos.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar gasto
     *
     * @param Gasto $gasto
     * @return JsonResponse|Response
     */
    public function destroy(Gasto $gasto): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->gastoService->eliminar($gasto);
            DB::commit();
            return response()->JSON([
                'sw' => true,
                'message' => 'El registro se eliminó correctamente'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }
}
