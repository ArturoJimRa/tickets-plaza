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
                'nombre' => 'required|unique:roles,nombre',
                'prefijo_folio' => 'nullable|string|max:10',

                // admin | gestion | solicitante
                'tipo_acceso' => 'required|in:admin,gestion,solicitante'
            ],
            [
                'nombre.unique' => 'Este rol ya existe',
                'tipo_acceso.required' => 'Seleccione el tipo de acceso'
            ]
        );

        // ===============================
        // PREFIJO
        // NO MODIFICAMOS TU LÓGICA
        // ===============================
        $prefijo = $request->filled('prefijo_folio')
            ? strtoupper(trim($request->prefijo_folio))
            : null;

        DB::table('roles')->insert([
            'nombre' => $request->nombre,
            'prefijo_folio' => $prefijo,
            'tipo_acceso' => $request->tipo_acceso,
        ]);

        return redirect('/admin/roles')
            ->with('success', 'Rol creado correctamente');
    }

    /* ===============================
       FORMULARIO EDITAR
    =============================== */
    public function edit($id)
    {
        if (session('rol') !== 'Admin') abort(403);

        $rol = DB::table('roles')
            ->where('id', $id)
            ->first();

        if (!$rol) {
            abort(404);
        }

        return view('admin.roles.edit', compact('rol'));
    }

    /* ===============================
       ACTUALIZAR
    =============================== */
    public function update(Request $request, $id)
    {
        if (session('rol') !== 'Admin') abort(403);

        $rol = DB::table('roles')
            ->where('id', $id)
            ->first();

        if (!$rol) {
            abort(404);
        }

        $request->validate([
            'nombre' => 'required|string|max:255|unique:roles,nombre,' . $id,
            'prefijo_folio' => 'nullable|string|max:10',

            // admin | gestion | solicitante
            'tipo_acceso' => 'required|in:admin,gestion,solicitante'
        ]);

        // ===============================
        // PREFIJO
        // NO MODIFICAMOS TU LÓGICA
        // ===============================
        $prefijo = $request->filled('prefijo_folio')
            ? strtoupper(trim($request->prefijo_folio))
            : null;

        DB::table('roles')
            ->where('id', $id)
            ->update([
                'nombre' => $request->nombre,
                'prefijo_folio' => $prefijo,
                'tipo_acceso' => $request->tipo_acceso,
            ]);

        return redirect('/admin/roles')
            ->with('success', 'Rol actualizado correctamente');
    }
}