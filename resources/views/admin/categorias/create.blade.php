@extends('layouts.app')

@section('title', 'Nueva Categoría')

@section('content')

<h4 class="mb-4">➕ Nueva Categoría de Ticket</h4>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="/admin/categorias">
    @csrf

    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="nombre" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Descripción</label>
        <textarea name="descripcion" class="form-control" required></textarea>
    </div>

    <div class="mb-3">
        <label>Área que atenderá esta categoría</label>
        <select name="rol_destino_id" class="form-select" required>
            <option value="">Seleccione el área</option>
            @foreach($roles as $rol)
                @if($rol->nombre !== 'Admin' && $rol->nombre !== 'Unidad')
                    <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                 @endif
            @endforeach
        </select>
    </div>

    <button class="btn btn-success">Guardar</button>
    <a href="/admin/categorias" class="btn btn-secondary">Cancelar</a>
</form>

@endsection