@extends('layouts.app')

@section('title', 'Nueva Marca')

@section('content')

<h4>➕ Nueva Marca</h4>

<form method="POST" action="/admin/marcas">
    @csrf

    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="nombre" class="form-control" required>
        @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label>Estado</label>
        <select name="estado" class="form-select" required>
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
        </select>
    </div>

    <button class="btn btn-success">Guardar</button>
    <a href="/admin/marcas" class="btn btn-secondary">Cancelar</a>
</form>

@endsection