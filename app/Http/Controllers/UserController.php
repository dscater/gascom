<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Certificado;
use App\Models\Cliente;
use App\Models\Gasto;
use App\Models\Jugador;
use App\Models\LoginUser;
use App\Models\Participante;
use App\Models\Partido;
use App\Models\User;
use App\Services\LoginUserService;
use App\Services\PermisoService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{


    public function __construct(private LoginUserService $login_user_service) {}

    public function permisosUsuario(Request $request)
    {
        $permisoService = new PermisoService();
        return response()->JSON([
            "permisos" => $permisoService->getPermisosUser()
        ]);
    }

    public function getUser()
    {
        return response()->JSON([
            "user" => Auth::user()
        ]);
    }

    public static function getInfoBoxUser()
    {
        $permisos = [];
        $array_infos = [];
        if (Auth::check()) {
            $oUser = new User();
            $permisos = $oUser->permisos;
            if ($permisos == '*' || (is_array($permisos) && in_array('gastos.index', $permisos))) {
                $gastos = Gasto::count();
                $array_infos[] = [
                    'label' => 'GASTOS',
                    'cantidad' => $gastos,
                    'color' => 'bgWhite',
                    'icon' => "fa-list-alt",
                    "url" => "gastos.index"
                ];
            }

            if ($permisos == '*' || (is_array($permisos) && in_array('participantes.index', $permisos))) {
                $participantes = Participante::count();
                $array_infos[] = [
                    'label' => 'PARTICIPANTES',
                    'cantidad' => $participantes,
                    'color' => 'bgWhite',
                    'icon' => "fa-table",
                    "url" => "participantes.index"
                ];
            }
        }


        return $array_infos;
    }
}
