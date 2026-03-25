<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubcategoriaTicket;
use App\Models\CategoriaTicket;

class SubcategoriaController extends Controller
{
    // LISTAR
    public function index()
    {
        $subcategorias = SubcategoriaTicket::with('categoria')->get();
        return view('admin.subcategorias.index', compact('subcategorias'));
    }

    // FORMULARIO CREAR
    public function create()
    {
        $categorias = CategoriaTicket::all();
        return view('admin.subcategorias.create', compact('categorias'));
    }

    // GUARDAR
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'categoria_id' => 'required|exists:categorias_ticket,id'
        ]);

        SubcategoriaTicket::create([
            'nombre' => $request->nombre,
            'categoria_id' => $request->categoria_id
        ]);

        return redirect('/admin/subcategorias')
            ->with('success', 'Subcategoría creada correctamente');
    }
 
    public function getByCategoria($categoria_id)
{
    $subcategorias = \App\Models\SubcategoriaTicket::where('categoria_id', $categoria_id)->get();

    return response()->json($subcategorias);
}
}