<?php

namespace App\Http\Controllers;

use App\Models\Unidad;
use App\Models\Marca;
use Illuminate\Http\Request;

class UnidadController extends Controller
{
    public function index()
    {
        $unidades = Unidad::with('marca')->get();
        return view('admin.unidades.index', compact('unidades'));
    }

    public function create()
    {
        $marcas = Marca::where('estado', 'activo')->get();
        return view('admin.unidades.create', compact('marcas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'razon_social' => 'nullable|string|max:255',
            'marca_id' => 'required|exists:marcas,id'
        ]);

        Unidad::create([
            'nombre' => $request->nombre,
            'razon_social' => $request->razon_social,
            'marca_id' => $request->marca_id,
            'estado' => 'activo'
        ]);

        return redirect()->route('unidades.index')
            ->with('success', 'Unidad registrada correctamente');
    }

    public function edit(Unidad $unidad)
    {
        $marcas = Marca::where('estado', 'activo')->get();
        return view('admin.unidades.edit', compact('unidad', 'marcas'));
    }

    public function update(Request $request, Unidad $unidad)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'razon_social' => 'nullable|string|max:255',
            'marca_id' => 'required|exists:marcas,id'
        ]);

        $unidad->update([
            'nombre' => $request->nombre,
            'razon_social' => $request->razon_social,
            'marca_id' => $request->marca_id
        ]);

        return redirect()->route('unidades.index')
            ->with('success', 'Unidad actualizada correctamente');
    }

    public function cambiarEstado(Unidad $unidad)
    {
        $unidad->estado = $unidad->estado === 'activo' ? 'inactivo' : 'activo';
        $unidad->save();

        return redirect()->back();
    }
}