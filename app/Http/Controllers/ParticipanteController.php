<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParticipanteStoreRequest;
use App\Http\Requests\ParticipanteUpdateRequest;
use App\Models\Participante;
use App\Models\User;
use App\Services\ParticipanteService;
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

class ParticipanteController extends Controller
{
    public function __construct(private ParticipanteService $participanteService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): ResponseInertia
    {
        return Inertia::render("Admin/Participantes/Index");
    }

    /**
     * Listado de participantes sin ids: 1 y 2
     *
     * @return JsonResponse
     */
    public function listado(Request $request): JsonResponse
    {
        return response()->JSON([
            "participantes" => $this->participanteService->listado(
                $request->input("campeonato_id", null),
                $request->input("sin_inscripcion", false),
                $request->input("participante_id", null),
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

        $participantes = $this->participanteService->listadoPaginado($perPage, $page, $search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $arrayOrderBy);
        return response()->JSON([
            "data" => $participantes->items(),
            "total" => $participantes->total(),
            "lastPage" => $participantes->lastPage()
        ]);
    }

    /**
     * Registrar un nuevo participante
     *
     * @param ParticipanteStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(ParticipanteStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el Participante
            $this->participanteService->crear($request->validated());
            DB::commit();
            return redirect()->route("participantes.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un participante
     *
     * @param Participante $participante
     * @return JsonResponse
     */
    public function show(Participante $participante): JsonResponse
    {
        return response()->JSON($participante);
    }

    public function update(Participante $participante, ParticipanteUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar participante
            $this->participanteService->actualizar($request->validated(), $participante);
            DB::commit();
            return redirect()->route("participantes.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar participante
     *
     * @param Participante $participante
     * @return JsonResponse|Response
     */
    public function destroy(Participante $participante): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->participanteService->eliminar($participante);
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
