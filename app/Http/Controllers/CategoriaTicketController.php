<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\CategoriaTicket;

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
public function edit($id)
{
    $categoria = DB::table('categorias_ticket')->where('id', $id)->first();
    $roles = DB::table('roles')->get();

    return view('admin.categorias.edit', compact('categoria', 'roles'));
}
public function update(Request $request, $id)
{
    $request->validate([
        'nombre' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'rol_destino_id' => 'required|integer',
        'estado' => 'required|in:activo,inactivo',
    ]);

    DB::table('categorias_ticket')
        ->where('id', $id)
        ->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'rol_destino_id' => $request->rol_destino_id,
            'estado' => $request->estado,
        ]);

    return redirect('/admin/categorias')
        ->with('success', 'Categoría actualizada correctamente');
}
public function destroy($id)
{
    if (session('rol') !== 'Admin') abort(403);

    $categoria = DB::table('categorias_ticket')->where('id', $id)->first();

    // Normalizar estado
    $estadoActual = strtolower(trim($categoria->estado));

    // Cambiar estado
    $nuevoEstado = $estadoActual == 'activo' ? 'inactivo' : 'activo';

    DB::table('categorias_ticket')
        ->where('id', $id)
        ->update(['estado' => $nuevoEstado]);

    // Mensaje dinámico
    $mensaje = $nuevoEstado == 'activo'
        ? 'Categoría activada correctamente'
        : 'Categoría desactivada correctamente';

    return redirect('/admin/categorias')
        ->with('success', $mensaje);
}
}