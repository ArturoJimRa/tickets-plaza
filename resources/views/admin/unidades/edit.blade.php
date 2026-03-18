@extends('layouts.app')

@section('title', 'Editar Unidad')

@section('content')
<div class="container mt-4">

    {{-- TÍTULO --}}
    <div class="mb-3">
        <h4 class="fw-bold">✏️ Editar Unidad</h4>
        <p class="text-muted mb-0">
            Actualiza la información de la unidad seleccionada
        </p>
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

            <form method="POST" action="{{ route('unidades.update', $unidad) }}">
                @csrf
                @method('PUT')

                {{-- NOMBRE --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nombre de la unidad</label>
                    <input type="text"
                           name="nombre"
                           class="form-control"
                           value="{{ old('nombre', $unidad->nombre) }}"
                           required>
                </div>

                {{-- MARCA --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Marca</label>
                    <select name="marca_id" class="form-select" required>
                        @foreach($marcas as $marca)
                            <option value="{{ $marca->id }}"
                                {{ old('marca_id', $unidad->marca_id) == $marca->id ? 'selected' : '' }}>
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
                        🔄 Actualizar Unidad
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection