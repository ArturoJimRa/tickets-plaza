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
        <label class="form-label">Nombre del rol</label>
        <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Prefijo de Folio (Opcional)</label>
        <input type="text" name="prefijo_folio" class="form-control" maxlength="10" placeholder="Ej. TI, MKT, RH" value="{{ old('prefijo_folio') }}">
        <div class="form-text">Déjalo vacío si este rol no generará folios propios (ej. Admin, Unidad).</div>
    </div>

    <button type="submit" class="btn btn-success">Guardar</button>
    <a href="/admin/roles" class="btn btn-secondary">Cancelar</a>
</form>

@endsection