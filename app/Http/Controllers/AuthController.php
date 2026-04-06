<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1️⃣ Validar datos
        $request->validate([
            'correo'     => 'required|email',
            'contrasena' => 'required'
        ]);

        // 2️⃣ Buscar usuario con su ROL (JOIN)
        $usuario = DB::table('usuarios')
            ->join('roles', 'usuarios.rol_id', '=', 'roles.id')
            ->select(
                'usuarios.id',
                'usuarios.nombre',
                'usuarios.contrasena',
                'usuarios.estado',
                'usuarios.rol_id',            // ✅ NECESARIO
                'usuarios.es_jefe',
                'roles.nombre as rol'
            )
            ->where('usuarios.correo', $request->correo)
            ->first();

        // 3️⃣ Usuario existe
        if (!$usuario) {
            return back()->with('error', 'Usuario no encontrado');
        }

        // 4️⃣ Verificar contraseña
        if (!Hash::check($request->contrasena, $usuario->contrasena)) {
            return back()->with('error', 'Contraseña incorrecta');
        }

        // 5️⃣ Verificar estado
        if ($usuario->estado !== 'activo') {
            return back()->with('error', 'Usuario inactivo');
        }

        // 🔥 LIMPIAR SESIÓN ANTERIOR (IMPORTANTE)
        session()->flush();

        // 6️⃣ Crear sesión (AQUÍ ESTABA EL ERROR)
        session([
            'usuario_id' => $usuario->id,
            'nombre'     => $usuario->nombre,
            'rol'        => $usuario->rol,     // Admin | Sistemas | Unidad | MKT | etc
            'rol_id'     => $usuario->rol_id,  // ✅ CLAVE PARA FILTRAR TICKETS
            'es_jefe'    => $usuario->es_jefe,
        ]);

        // 7️⃣ Redirigir
        return redirect('/dashboard');
    }

    public function cambiarPassword(Request $request)
    {
        $request->validate([
            'actual'        => 'required',
            'nueva'         => 'required|min:6',
            'confirmacion'  => 'required|same:nueva',
        ]);

        $usuario = DB::table('usuarios')
            ->where('id', session('usuario_id'))
            ->first();

        if (!$usuario) {
            return back()->with('error', 'Usuario no encontrado');
        }

        if (!Hash::check($request->actual, $usuario->contrasena)) {
            return back()->with('error', 'La contraseña actual es incorrecta');
        }

        DB::table('usuarios')
            ->where('id', $usuario->id)
            ->update([
                'contrasena' => Hash::make($request->nueva)
            ]);

        return back()->with('ok', 'Contraseña actualizada correctamente');
    }
}