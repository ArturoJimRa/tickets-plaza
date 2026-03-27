@extends('layouts.app')

@section('title', 'Editar Rol')

@section('content')

<div class="container mt-4">
    <h4 class="fw-bold mb-3">Editar Rol</h4>

    <div class="card shadow-sm">
        <div class="card-body">

            {{-- ERRORES --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/admin/roles/{{ $rol->id }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nombre del Rol</label>
                    <input 
                        type="text" 
                        name="nombre" 
                        class="form-control"
                        value="{{ old('nombre', $rol->nombre) }}" 
                        required>
                </div>

                <button class="btn btn-primary">
                    Actualizar
                </button>

                <a href="/admin/roles" class="btn btn-secondary">
                    Cancelar
                </a>

            </form>

        </div>
    </div>
</div>

@endsection