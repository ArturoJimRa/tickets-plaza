<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
{
    $usuarios = DB::table('usuarios')
        ->leftJoin('roles', 'usuarios.rol_id', '=', 'roles.id')
        ->leftJoin('unidades', 'usuarios.unidad_id', '=', 'unidades.id')
        ->select(
            'usuarios.id',
            'usuarios.nombre',
            'usuarios.correo',
            'roles.nombre as rol',
            'unidades.nombre as unidad',
            'usuarios.estado'
        )
        ->get();

    return view('usuarios.index', compact('usuarios'));
}

    public function store(Request $request)
{
    try {
        $request->validate([
            'nombre'     => 'required',
            'correo'     => 'required|email|unique:usuarios,correo',
            'contrasena' => 'required|min:6',
            'rol_id'     => 'required',
            'unidad_id'  => 'nullable'
        ]);

        DB::table('usuarios')->insert([
            'nombre'     => $request->nombre,
            'correo'     => $request->correo,
            'contrasena' => Hash::make($request->contrasena),
            'rol_id'     => $request->rol_id,
            'unidad_id'  => $request->unidad_id,
            'estado'     => 'activo',
        
        ]);

        return redirect('/usuarios')->with('success', 'Usuario creado correctamente');

    } catch (\Exception $e) {
        return back()
            ->withInput()
            ->with('error', 'Error al crear usuario: '.$e->getMessage());
    }
}


    public function cambiarEstado($id)
{
    // 1️⃣ No permitir que el usuario logueado se desactive a sí mismo
    if (session('usuario_id') == $id) {
        return back()->with('error', 'No puedes desactivarte a ti mismo');
    }

    // 2️⃣ Obtener usuario objetivo
    $usuario = DB::table('usuarios')->where('id', $id)->first();

    if (!$usuario) {
        abort(404);
    }

    // 3️⃣ No permitir desactivar administradores
    if ($usuario->rol_id == 1) { // 1 = Admin
        return back()->with('error', 'No se puede desactivar un administrador');
    }

    // 4️⃣ Verificar que quede al menos un administrador activo
    $adminsActivos = DB::table('usuarios')
        ->where('rol_id', 1)
        ->where('estado', 'activo')
        ->count();

    if ($usuario->rol_id == 1 && $adminsActivos <= 1) {
        return back()->with('error', 'Debe existir al menos un administrador activo');
    }

    // 5️⃣ Cambiar estado
    $nuevoEstado = $usuario->estado === 'activo' ? 'inactivo' : 'activo';

    DB::table('usuarios')
        ->where('id', $id)
        ->update(['estado' => $nuevoEstado]);

    return redirect('/usuarios')->with('ok', 'Estado actualizado correctamente');
}

public function edit($id)
{
    $usuario = DB::table('usuarios')->where('id', $id)->first();

    $roles = DB::table('roles')->get();

    $unidades = DB::table('unidades')->get();

    return view('usuarios.edit', compact('usuario','roles','unidades'));
}
public function update(Request $request, $id)
{
    $request->validate([
        'nombre' => 'required',
        'correo' => 'required|email',
        'rol_id' => 'required'
    ]);

    DB::table('usuarios')
        ->where('id', $id)
        ->update([
            'nombre' => $request->nombre,
            'correo' => $request->correo,
            'rol_id' => $request->rol_id,
            'unidad_id' => $request->unidad_id,
            'contrasena'=> $request->contrasena
        ]);

    return redirect('/usuarios')
        ->with('ok','Usuario actualizado correctamente');
}
}  