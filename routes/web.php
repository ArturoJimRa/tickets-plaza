<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CategoriaTicketController;
use App\Http\Controllers\SubcategoriaController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\UnidadController;


Route::get('/', function () {
    return redirect('/login');
});

/*
|-------------------------------------------------------------------------- 
| LOGIN
|-------------------------------------------------------------------------- 
*/

Route::get('/login', function () {
    if (session()->has('usuario_id')) {
        return redirect('/dashboard');
    }
    return view('login');
})->name('login');

Route::post('/login', [AuthController::class, 'login']);

/*
|-------------------------------------------------------------------------- 
| DASHBOARD
|-------------------------------------------------------------------------- 
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('authcheck');

/*
|-------------------------------------------------------------------------- 
| USUARIOS (SOLO ADMIN)
|-------------------------------------------------------------------------- 
*/

Route::get('/usuarios', [UsuarioController::class, 'index'])
    ->middleware(['authcheck', 'role:Admin']);

Route::get('/usuarios/create', function () {
    return view('usuarios.create');
})->middleware(['authcheck', 'role:Admin']);

Route::post('/usuarios', [UsuarioController::class, 'store'])
    ->middleware(['authcheck', 'role:Admin']);

Route::post('/usuarios/{id}/estado', [UsuarioController::class, 'cambiarEstado'])
    ->middleware(['authcheck', 'role:Admin']);

Route::get('/usuarios/{id}/edit', [UsuarioController::class, 'edit'])
    ->name('usuarios.edit');

Route::put('/usuarios/{id}', [UsuarioController::class, 'update'])
    ->name('usuarios.update');

/*
|-------------------------------------------------------------------------- 
| ADMIN (CATEGORÍAS, ROLES, MARCAS, UNIDADES)
|-------------------------------------------------------------------------- 
*/

Route::middleware(['web'])->group(function () {

    // CATEGORÍAS
    Route::get('/admin/categorias', [CategoriaTicketController::class, 'index']);
    Route::get('/admin/categorias/create', [CategoriaTicketController::class, 'create']);
    Route::post('/admin/categorias', [CategoriaTicketController::class, 'store']);
    // EDITAR CATEGORÍA
    Route::get('/admin/categorias/{id}/edit', [CategoriaTicketController::class, 'edit']);
    
    // ACTUALIZAR CATEGORÍA
    Route::put('/admin/categorias/{id}', [CategoriaTicketController::class, 'update']);

    // SUBCATEGORÍAS (SOLO ADMIN)
    Route::get('/admin/subcategorias', [SubcategoriaController::class, 'index'])
    ->middleware(['authcheck', 'role:Admin']);

    Route::get('/admin/subcategorias/create', [SubcategoriaController::class, 'create'])
    ->middleware(['authcheck', 'role:Admin']);

    Route::post('/admin/subcategorias', [SubcategoriaController::class, 'store'])
    ->middleware(['authcheck', 'role:Admin']);
    
    Route::get('/admin/subcategorias/{categoria}', [SubcategoriaController::class, 'getByCategoria']);

    // EDITAR SUBCATEGORÍA
    Route::get('/admin/subcategorias/{id}/edit', [SubcategoriaController::class, 'edit']);

    // ACTUALIZAR SUBCATEGORÍA
    Route::put('/admin/subcategorias/{id}', [SubcategoriaController::class, 'update']);

    Route::delete('/admin/categorias/{id}', [CategoriaTicketController::class, 'destroy']);


    // ROLES
    Route::get('/admin/roles', [RolController::class, 'index']);
    Route::get('/admin/roles/create', [RolController::class, 'create']);
    Route::post('/admin/roles', [RolController::class, 'store']);
    // EDITAR ROL
    Route::get('/admin/roles/{id}/edit', [RolController::class, 'edit']);

    // ACTUALIZAR ROL
    Route::put('/admin/roles/{id}', [RolController::class, 'update']);

    // MARCAS (SOLO ADMIN)
    Route::get('/admin/marcas', [MarcaController::class, 'index'])
        ->middleware(['authcheck', 'role:Admin'])
        ->name('marcas.index');

    Route::get('/admin/marcas/create', [MarcaController::class, 'create'])
        ->middleware(['authcheck', 'role:Admin'])
        ->name('marcas.create');

    Route::post('/admin/marcas', [MarcaController::class, 'store'])
        ->middleware(['authcheck', 'role:Admin'])
        ->name('marcas.store');

    Route::get('/admin/marcas/{marca}/edit', [MarcaController::class, 'edit'])
        ->middleware(['authcheck', 'role:Admin'])
        ->name('marcas.edit');

    Route::put('/admin/marcas/{marca}', [MarcaController::class, 'update'])
        ->middleware(['authcheck', 'role:Admin'])
        ->name('marcas.update');

    Route::delete('/admin/marcas/{marca}', [MarcaController::class, 'destroy'])
        ->middleware(['authcheck', 'role:Admin'])
        ->name('marcas.destroy');
    
    Route::get('/admin/unidades', [UnidadController::class, 'index'])
        ->name('unidades.index');

    Route::get('/admin/unidades/create', [UnidadController::class, 'create'])
        ->name('unidades.create');

    Route::post('/admin/unidades', [UnidadController::class, 'store'])
        ->name('unidades.store');

    Route::get('/admin/unidades/{unidad}/edit', [UnidadController::class, 'edit'])
        ->name('unidades.edit');

    Route::put('/admin/unidades/{unidad}', [UnidadController::class, 'update'])
        ->name('unidades.update');

    Route::post('/admin/unidades/{unidad}/estado', [UnidadController::class, 'cambiarEstado'])
        ->name('unidades.estado');
});

/*
|-------------------------------------------------------------------------- 
| CAMBIO DE CONTRASEÑA
|-------------------------------------------------------------------------- 
*/

Route::get('/cambiar-password', function () {
    return view('auth.cambiar_password');
})->middleware('authcheck');

Route::post('/cambiar-password', [AuthController::class, 'cambiarPassword'])
    ->middleware('authcheck');

/*
|-------------------------------------------------------------------------- 
| TICKETS
|-------------------------------------------------------------------------- 
*/

Route::get('/tickets/create', [TicketController::class, 'create'])
    ->middleware(['authcheck']);

Route::post('/tickets', [TicketController::class, 'store'])
    ->middleware(['authcheck']);

Route::get('/tickets', [TicketController::class, 'index'])
    ->middleware('authcheck');

Route::get('/tickets/{id}', [TicketController::class, 'show'])
    ->middleware('authcheck');

Route::get('/mis-tickets', [TicketController::class, 'misTickets'])
    ->middleware('authcheck');

Route::post('/tickets/{id}/responder', [TicketController::class, 'responder'])
    ->middleware(['authcheck']);

Route::post('/tickets/{id}/asignar', [TicketController::class, 'asignar'])
    ->middleware(['authcheck']);

Route::post('/tickets/{id}/cerrar', [TicketController::class, 'cerrar'])
    ->middleware(['authcheck']);

/*
|-------------------------------------------------------------------------- 
| LOGOUT
|-------------------------------------------------------------------------- 
*/

Route::get('/logout', function () {
    session()->flush();
    return redirect('/login');
})->name('logout');