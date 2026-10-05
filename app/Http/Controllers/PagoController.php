<?php

namespace App\Http\Controllers;

use App\Http\Requests\PagoStoreRequest;
use App\Http\Requests\PagoUpdateRequest;
use App\Models\Pago;
use App\Models\User;
use App\Services\PagoService;
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

class PagoController extends Controller
{
    public function __construct(private PagoService $pagoService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): ResponseInertia
    {
        return Inertia::render("Admin/Pagos/Index");
    }

    /**
     * Listado de pagos sin ids: 1 y 2
     *
     * @return JsonResponse
     */
    public function listado(Request $request): JsonResponse
    {
        return response()->JSON([
            "pagos" => $this->pagoService->listado(
                $request->input("campeonato_id", null),
                $request->input("sin_inscripcion", false),
                $request->input("pago_id", null),
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

        $pagos = $this->pagoService->listadoPaginado($perPage, $page, $search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $arrayOrderBy);
        return response()->JSON([
            "data" => $pagos->items(),
            "total" => $pagos->total(),
            "lastPage" => $pagos->lastPage()
        ]);
    }

    public function create(): ResponseInertia
    {
        return Inertia::render("Admin/Pagos/Create");
    }


    /**
     * Registrar un nuevo pago
     *
     * @param PagoStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(PagoStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el Pago
            $this->pagoService->crear($request->validated());
            DB::commit();
            return redirect()->route("pagos.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un pago
     *
     * @param Pago $pago
     * @return JsonResponse
     */
    public function show(Pago $pago): JsonResponse
    {
        return response()->JSON($pago);
    }

    public function edit(Pago $pago): ResponseInertia
    {
        return Inertia::render("Admin/Pagos/Edit", compact("pago"));
    }


    public function update(Pago $pago, PagoUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar pago
            $this->pagoService->actualizar($request->validated(), $pago);
            DB::commit();
            return redirect()->route("pagos.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar pago
     *
     * @param Pago $pago
     * @return JsonResponse|Response
     */
    public function destroy(Pago $pago): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->pagoService->eliminar($pago);
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
