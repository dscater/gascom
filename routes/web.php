<?php

use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ParticipanteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('inicio');
    }
    return Inertia::render('Auth/Login');
});

Route::get('/login', function () {
    if (Auth::check()) {
        return redirect()->route('inicio');
    }
    return Inertia::render('Auth/Login');
})->name("login");

Route::get("configuracions/getConfiguracion", [ConfiguracionController::class, 'getConfiguracion'])->name("configuracions.getConfiguracion");

Route::get('/clear-cache', function () {
    Artisan::call('config:cache');
    Artisan::call('config:clear');
    Artisan::call('optimize');
    return 'Cache eliminado <a href="/">Ir al inicio</a>';
})->name('clear.cache');

Route::get("sincronizarInicio", [SincronizacionController::class, 'sincronizarInicio']);
Route::get("sincronizarClientesTramitador", [SincronizacionController::class, 'sincronizarClientesTramitador']);

// ADMINISTRACION
Route::middleware(['auth', 'permisoUsuario'])->prefix("admin")->group(function () {
    // INICIO
    Route::get('/inicio', [InicioController::class, 'inicio'])->name('inicio');

    // CONFIGURACION
    Route::resource("configuracions", ConfiguracionController::class)->only(
        ["index", "show", "update"]
    );

    // USUARIO
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('profile/update_foto', [ProfileController::class, 'update_foto'])->name('profile.update_foto');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get("getUser", [UserController::class, 'getUser'])->name('users.getUser');
    Route::get("permisosUsuario", [UserController::class, 'permisosUsuario']);

    // USUARIOS
    Route::put("usuarios/password/{user}", [UsuarioController::class, 'actualizaPassword'])->name("usuarios.password");
    Route::get("usuarios/paginado", [UsuarioController::class, 'paginado'])->name("usuarios.paginado");
    Route::get("usuarios/listado", [UsuarioController::class, 'listado'])->name("usuarios.listado");
    Route::get("usuarios/listado/byTipo", [UsuarioController::class, 'byTipo'])->name("usuarios.byTipo");
    Route::get("usuarios/show/{user}", [UsuarioController::class, 'show'])->name("usuarios.show");
    Route::put("usuarios/update/{user}", [UsuarioController::class, 'update'])->name("usuarios.update");
    Route::delete("usuarios/{user}", [UsuarioController::class, 'destroy'])->name("usuarios.destroy");
    Route::resource("usuarios", UsuarioController::class)->only(
        ["index", "store"]
    );

    // TIPO USUARIOS
    Route::get("tipo_usuarios/listado", [TipoUsuarioController::class, 'listado'])->name("tipo_usuarios.listado");

    // GASTOS
    Route::get("gastos/paginado", [GastoController::class, 'paginado'])->name("gastos.paginado");
    Route::get("gastos/listado", [GastoController::class, 'listado'])->name("gastos.listado");
    Route::patch("gastos/finalizar/{campeonato}", [GastoController::class, 'finalizar'])->name("gastos.finalizar");
    Route::resource("gastos", GastoController::class)->only(
        ["index", "store", "edit", "show", "update", "destroy"]
    );

    // PARTICIPANTES
    Route::get("participantes/paginado", [ParticipanteController::class, 'paginado'])->name("participantes.paginado");
    Route::get("participantes/listado", [ParticipanteController::class, 'listado'])->name("participantes.listado");
    Route::patch("participantes/finalizar/{campeonato}", [ParticipanteController::class, 'finalizar'])->name("participantes.finalizar");
    Route::resource("participantes", ParticipanteController::class)->only(
        ["index", "store", "edit", "show", "update", "destroy"]
    );

    // PAGOS
    Route::get("pagos/paginado", [PagoController::class, 'paginado'])->name("pagos.paginado");
    Route::get("pagos/listado", [PagoController::class, 'listado'])->name("pagos.listado");
    Route::get("pagos/distribuir/{pago}", [PagoController::class, 'distribuir'])->name("pagos.distribuir");
    Route::resource("pagos", PagoController::class)->only(
        ["index", "create", "store", "edit", "show", "update", "destroy"]
    );

    // REPORTES
    Route::get('reportes/usuarios', [ReporteController::class, 'usuarios'])->name("reportes.usuarios");
    Route::get('reportes/r_usuarios', [ReporteController::class, 'r_usuarios'])->name("reportes.r_usuarios");
});
require __DIR__ . '/auth.php';
