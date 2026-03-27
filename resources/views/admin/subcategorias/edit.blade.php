@extends('layouts.app')

@section('title', 'Editar Subcategoría')

@section('content')

<div class="container mt-4">
    <h4 class="fw-bold mb-3">Editar Subcategoría</h4>

    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST" action="/admin/subcategorias/{{ $subcategoria->id }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control"
                           value="{{ $subcategoria->nombre }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Categoría</label>
                    <select name="categoria_id" class="form-control" required>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}"
                                {{ $subcategoria->categoria_id == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button class="btn btn-primary">Actualizar</button>
                <a href="/admin/subcategorias" class="btn btn-secondary">Cancelar</a>

            </form>

        </div>
    </div>
</div>

@endsection