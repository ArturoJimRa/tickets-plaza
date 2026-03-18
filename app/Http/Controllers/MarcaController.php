<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    public function index()
    {
        $marcas = Marca::orderBy('nombre')->get();
        return view('admin.marcas.index', compact('marcas'));
    }

    public function create()
    {
        return view('admin.marcas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:marcas,nombre',
            'estado' => 'required|in:activo,inactivo'
        ], [
            'nombre.unique' => 'La marca ya existe'
        ]);

        Marca::create($request->all());

        return redirect()->route('marcas.index')
            ->with('success', 'Marca registrada correctamente');
    }

    public function edit(Marca $marca)
    {
        return view('admin.marcas.edit', compact('marca'));
    }

    public function update(Request $request, Marca $marca)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:marcas,nombre,' . $marca->id,
            'estado' => 'required|in:activo,inactivo'
        ]);

        $marca->update($request->all());

        return redirect()->route('marcas.index')
            ->with('success', 'Marca actualizada correctamente');
    }
    public function destroy(Marca $marca)
{
    $marca->estado = 'inactivo';
    $marca->save();

    return redirect()
        ->route('marcas.index')
        ->with('success', 'Marca desactivada correctamente');
}
}