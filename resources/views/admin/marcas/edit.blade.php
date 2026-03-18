@extends('layouts.app')

@section('title', 'Editar Marca')

@section('content')

<h4 class="mb-4">✏️ Editar Marca</h4>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('marcas.update', $marca) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre"
               value="{{ old('nombre', $marca->nombre) }}"
               class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Estado</label>
        <select name="estado" class="form-select">
            <option value="activo" {{ $marca->estado == 'activo' ? 'selected' : '' }}>
                Activo
            </option>
            <option value="inactivo" {{ $marca->estado == 'inactivo' ? 'selected' : '' }}>
                Inactivo
            </option>
        </select>
    </div>

    <button class="btn btn-primary">Actualizar</button>
    <a href="{{ route('marcas.index') }}" class="btn btn-secondary">Cancelar</a>
</form>

@endsection