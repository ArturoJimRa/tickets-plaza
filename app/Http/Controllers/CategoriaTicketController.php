<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriaTicketController extends Controller
{
    /* ===============================
       LISTA DE CATEGORÍAS (ADMIN)
    =============================== */
    public function index()
    {
        if (session('rol') !== 'Admin') abort(403);

        $categorias = DB::table('categorias_ticket')
            ->join('roles', 'categorias_ticket.rol_destino_id', '=', 'roles.id')
            ->select(
                'categorias_ticket.*',
                'roles.nombre as rol_destino'
            )
            ->orderBy('categorias_ticket.nombre')
            ->get();

        return view('admin.categorias.index', compact('categorias'));
    }

    /* ===============================
       FORMULARIO CREAR
    =============================== */
    public function create()
{
    if (session('rol') !== 'Admin') abort(403);

    $roles = DB::table('roles')->get();

    return view('admin.categorias.create', compact('roles'));
}

    /* ===============================
       GUARDAR
    =============================== */
    public function store(Request $request)
{
    if (session('rol') !== 'Admin') abort(403);

    $request->validate(
        [
            'nombre' => 'required|unique:categorias_ticket,nombre',
            'descripcion' => 'required',
            'rol_destino_id' => 'required|exists:roles,id'
        ],
        [
            'nombre.unique' => 'Ya existe una categoría con ese nombre'
        ]
    );

    DB::table('categorias_ticket')->insert([
        'nombre' => $request->nombre,
        'descripcion' => $request->descripcion,
        'rol_destino_id' => $request->rol_destino_id,
        'estado' => 'activo'
    ]);

    return redirect('/admin/categorias')
        ->with('success', 'Categoría creada correctamente');
}
}