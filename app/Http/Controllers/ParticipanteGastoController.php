<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParticipanteGastoStoreRequest;
use App\Http\Requests\ParticipanteGastoUpdateRequest;
use App\Models\ParticipanteGasto;
use App\Models\User;
use App\Services\ParticipanteGastoService;
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

class ParticipanteGastoController extends Controller
{
    public function __construct(private ParticipanteGastoService $participante_gastoService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): ResponseInertia
    {
        return Inertia::render("Admin/ParticipanteGastos/Index");
    }

    /**
     * Listado de participante_gastos sin ids: 1 y 2
     *
     * @return JsonResponse
     */
    public function listado(Request $request): JsonResponse
    {
        return response()->JSON([
            "participante_gastos" => $this->participante_gastoService->listado(
                $request->input("campeonato_id", null),
                $request->input("sin_inscripcion", false),
                $request->input("participante_gasto_id", null),
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
            "participantes.nombre",
            "gastos.nombre",
        ];
        $columnsFilter = [];
        $columnsBetweenFilter = [];
        $arrayOrderBy = [];
        if ($orderBy && $orderAsc) {
            $arrayOrderBy = [
                [$orderBy, $orderAsc]
            ];
        }

        $participante_gastos = $this->participante_gastoService->listadoPaginado($perPage, $page, $search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $arrayOrderBy);
        return response()->JSON([
            "data" => $participante_gastos->items(),
            "total" => $participante_gastos->total(),
            "lastPage" => $participante_gastos->lastPage()
        ]);
    }

    /**
     * Registrar un nuevo participante_gasto
     *
     * @param ParticipanteGastoStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(ParticipanteGastoStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el ParticipanteGasto
            $this->participante_gastoService->crear($request->validated());
            DB::commit();
            return redirect()->route("participante_gastos.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un participante_gasto
     *
     * @param ParticipanteGasto $participante_gasto
     * @return JsonResponse
     */
    public function show(ParticipanteGasto $participante_gasto): JsonResponse
    {
        return response()->JSON($participante_gasto);
    }

    public function update(ParticipanteGasto $participante_gasto, ParticipanteGastoUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar participante_gasto
            $this->participante_gastoService->actualizar($request->validated(), $participante_gasto);
            DB::commit();
            return redirect()->route("participante_gastos.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar participante_gasto
     *
     * @param ParticipanteGasto $participante_gasto
     * @return JsonResponse|Response
     */
    public function destroy(ParticipanteGasto $participante_gasto): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->participante_gastoService->eliminar($participante_gasto);
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
