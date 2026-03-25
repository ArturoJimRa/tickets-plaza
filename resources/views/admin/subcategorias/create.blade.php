@extends('layouts.app')

@section('title', 'Nueva Subcategoría')

@section('content')

<div class="container">

    <h4 class="mb-4">➕ Nueva Subcategoría</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST" action="/admin/subcategorias">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text"
                           name="nombre"
                           class="form-control"
                           value="{{ old('nombre') }}"
                           placeholder="Ej. Vacante Chef"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Categoría</label>
                    <select name="categoria_id" class="form-select" required>
                        <option value="">Seleccione una categoría</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}"
                                {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="/admin/subcategorias" class="btn btn-secondary">
                        ⬅ Volver
                    </a>

                    <button type="submit" class="btn btn-success">
                        Guardar
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection