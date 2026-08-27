@extends('layouts.app')

@section('title', 'Nuevo Rol')

@section('content')

<div class="container mt-4">

    <h4 class="mb-4">➕ Crear Rol</h4>

    {{-- ERRORES --}}
    @if($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

    <div class="card shadow-sm">

        <div class="card-body">

            <form method="POST" action="/admin/roles">

                @csrf

                {{-- NOMBRE --}}
                <div class="mb-3">

                    <label class="form-label">
                        Nombre del rol
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        class="form-control"
                        value="{{ old('nombre') }}"
                        placeholder="Ej. SISTEMAS, MARKETING, UNIDAD"
                        required>

                </div>

                {{-- PREFIJO --}}
                <div class="mb-3">

                    <label class="form-label">
                        Prefijo de Folio (Opcional)
                    </label>

                    <input
                        type="text"
                        name="prefijo_folio"
                        class="form-control"
                        maxlength="10"
                        placeholder="Ej. TI, MKT, RH"
                        value="{{ old('prefijo_folio') }}">

                    <div class="form-text">
                        Déjalo vacío si este rol no generará folios propios
                        (ej. Admin, Unidad).
                    </div>

                </div>

                {{-- TIPO DE ACCESO --}}
                <div class="mb-3">

                    <label class="form-label">
                        Tipo de acceso
                    </label>

                    <select
                        name="tipo_acceso"
                        class="form-select"
                        required>

                        <option value="">
                            Seleccione un tipo de acceso
                        </option>

                        <option
                            value="gestion"
                            {{ old('tipo_acceso') === 'gestion' ? 'selected' : '' }}>

                            🛠 Gestión

                        </option>

                        <option
                            value="solicitante"
                            {{ old('tipo_acceso') === 'solicitante' ? 'selected' : '' }}>

                            📝 Solicitante

                        </option>

                        <option
                            value="admin"
                            {{ old('tipo_acceso') === 'admin' ? 'selected' : '' }}>

                            👑 Administrador

                        </option>

                    </select>

                    <div class="form-text">

                        <strong>Gestión:</strong>
                        puede atender y gestionar tickets.

                        <br>

                        <strong>Solicitante:</strong>
                        puede crear y consultar sus propios tickets.

                        <br>

                        <strong>Administrador:</strong>
                        tiene acceso administrativo al sistema.

                    </div>

                </div>

                {{-- BOTONES --}}
                <button type="submit" class="btn btn-success">
                    Guardar
                </button>

                <a href="/admin/roles" class="btn btn-secondary">
                    Cancelar
                </a>

            </form>

        </div>

    </div>

</div>

@endsection