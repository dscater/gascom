<?php

namespace App\Services;

use App\Models\Permiso;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class PermisoService
{
    protected $arrayPermisos = [
        "ADMINISTRADOR" => [
            "configuracions.index",
            "configuracions.create",
            "configuracions.edit",
            "configuracions.update",
            "configuracions.destroy",

            "usuarios.paginado",
            "usuarios.index",
            "usuarios.listado",
            "usuarios.create",
            "usuarios.store",
            "usuarios.edit",
            "usuarios.show",
            "usuarios.update",
            "usuarios.destroy",
            "usuarios.password",
            "usuarios.byTipo",

            "tipo_usuarios.listado",

            "gastos.paginado",
            "gastos.index",
            "gastos.listado",
            "gastos.create",
            "gastos.store",
            "gastos.edit",
            "gastos.show",
            "gastos.update",
            "gastos.destroy",

            "participantes.paginado",
            "participantes.index",
            "participantes.listado",
            "participantes.create",
            "participantes.store",
            "participantes.edit",
            "participantes.show",
            "participantes.update",
            "participantes.destroy",

            "participante_gastos.paginado",
            "participante_gastos.index",
            "participante_gastos.listado",
            "participante_gastos.create",
            "participante_gastos.store",
            "partieipante_gastos.edit",
            "participante_gastos.show",
            "participante_gastos.update",
            "participante_gastos.destroy",

            "pagos.paginado",
            "pagos.index",
            "pagos.listado",
            "pagos.create",
            "pagos.store",
            "pagos.edit",
            "pagos.show",
            "pagos.update",
            "pagos.destroy",
            "pagos.distribuir",
            "pagos.guardar_distribuir",

            "reportes.usuarios",
            "reportes.r_usuarios",
        ],
        "AUXILIAR" => [],
    ];



    public function getTiposUsuarios()
    {
        return array_keys($this->arrayPermisos);
    }

    /**
     * Obtener permisos de usuario logeado
     *
     * @return array
     */
    public function getPermisosUser(): array|string
    {
        $user = Auth::user();
        $permisos = [];
        if ($user) {
            return $this->arrayPermisos[$user->tipo];
        }

        return $permisos;
    }
}
