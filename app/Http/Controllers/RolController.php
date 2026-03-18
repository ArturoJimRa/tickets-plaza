<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolController extends Controller
{
    /* ===============================
       LISTA DE ROLES
    =============================== */
    public function index()
    {
        if (session('rol') !== 'Admin') abort(403);

        $roles = DB::table('roles')
            ->orderBy('nombre')
            ->get();

        return view('admin.roles.index', compact('roles'));
    }

    /* ===============================
       FORMULARIO CREAR
    =============================== */
    public function create()
    {
        if (session('rol') !== 'Admin') abort(403);

        return view('admin.roles.create');
    }

    /* ===============================
       GUARDAR
    =============================== */
    public function store(Request $request)
    {
        if (session('rol') !== 'Admin') abort(403);

        $request->validate(
            [
                'nombre' => 'required|unique:roles,nombre'
            ],
            [
                'nombre.unique' => 'Este rol ya existe'
            ]
        );

        DB::table('roles')->insert([
            'nombre' => $request->nombre
        ]);

        return redirect('/admin/roles')
            ->with('success', 'Rol creado correctamente');
    }
}