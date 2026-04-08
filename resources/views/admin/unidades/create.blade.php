@extends('layouts.app')

@section('title', 'Nueva Unidad')

@section('content')
<div class="container mt-4">

    {{-- TÍTULO --}}
    <div class="mb-3">
        <h4 class="fw-bold">➕ Nueva Unidad</h4>
        <p class="text-muted mb-0">Registra una nueva unidad y asígnala a una marca</p>
    </div>

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

    {{-- FORMULARIO --}}
    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST" action="{{ route('unidades.store') }}">
                @csrf

                {{-- NOMBRE --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nombre de la unidad</label>
                    <input type="text"
                           name="nombre"
                           class="form-control"
                           placeholder="Ej. Unidad Puebla Centro"
                           value="{{ old('nombre') }}"
                           required>
                </div>

                {{-- RAZÓN SOCIAL --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Razón Social</label>
                    <input type="text"
                           name="razon_social"
                           class="form-control"
                           placeholder="Ej. Mi Empresa S.A. de C.V."
                           value="{{ old('razon_social') }}">
                </div>

                {{-- MARCA --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Marca</label>
                    <select name="marca_id" class="form-select" required>
                        <option value="">Selecciona una marca</option>
                        @foreach($marcas as $marca)
                            <option value="{{ $marca->id }}"
                                {{ old('marca_id') == $marca->id ? 'selected' : '' }}>
                                {{ $marca->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- BOTONES --}}
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('unidades.index') }}" class="btn btn-secondary">
                        Cancelar
                    </a>

                    <button type="submit" class="btn btn-primary">
                        💾 Guardar Unidad
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection