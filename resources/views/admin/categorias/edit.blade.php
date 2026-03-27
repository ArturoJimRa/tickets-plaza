@extends('layouts.app')

@section('title', 'Editar Categoría')

@section('content')

<div class="container mt-4">
    <h4 class="fw-bold mb-3">Editar Categoría</h4>

    <div class="card shadow-sm">
        <div class="card-body">

            {{-- MENSAJES DE ERROR --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/admin/categorias/{{ $categoria->id }}">
                @csrf
                @method('PUT')

                {{-- NOMBRE --}}
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input 
                        type="text" 
                        name="nombre" 
                        class="form-control"
                        value="{{ old('nombre', $categoria->nombre) }}" 
                        required>
                </div>

                {{-- DESCRIPCIÓN --}}
                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea 
                        name="descripcion" 
                        class="form-control"
                        rows="3">{{ old('descripcion', $categoria->descripcion) }}</textarea>
                </div>

                {{-- ROL DESTINO --}}
                <div class="mb-3">
                    <label class="form-label">Rol Destino</label>
                    <select name="rol_destino_id" class="form-select" required>
                        @foreach($roles as $rol)
                            <option 
                                value="{{ $rol->id }}"
                                {{ old('rol_destino_id', $categoria->rol_destino_id) == $rol->id ? 'selected' : '' }}>
                                {{ $rol->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- ESTADO --}}
                <div class="mb-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select" required>
                        <option value="activo" {{ old('estado', $categoria->estado) == 'activo' ? 'selected' : '' }}>
                            Activo
                        </option>
                        <option value="inactivo" {{ old('estado', $categoria->estado) == 'inactivo' ? 'selected' : '' }}>
                            Inactivo
                        </option>
                    </select>
                </div>

                {{-- BOTONES --}}
                <button class="btn btn-primary">
                    Actualizar
                </button>

                <a href="/admin/categorias" class="btn btn-secondary">
                    Cancelar
                </a>

            </form>

        </div>
    </div>
</div>

@endsection