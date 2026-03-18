@extends('layouts.app')

@section('title', 'Nuevo Rol')

@section('content')

<h4 class="mb-4">➕ Crear Rol</h4>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="/admin/roles">
    @csrf

    <div class="mb-3">
        <label>Nombre del rol</label>
        <input type="text" name="nombre" class="form-control" required>
    </div>

    <button class="btn btn-success">Guardar</button>
    <a href="/admin/roles" class="btn btn-secondary">Cancelar</a>
</form>

@endsection