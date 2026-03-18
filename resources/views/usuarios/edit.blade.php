@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')

<div class="container mt-4">

    <h4 class="fw-bold mb-3">Editar Usuario</h4>

    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST" action="{{ route('usuarios.update', $usuario->id) }}">
                @csrf
                @method('PUT')

                {{-- NOMBRE --}}
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text"
                        name="nombre"
                        class="form-control"
                        value="{{ $usuario->nombre }}"
                        required>
                </div>

                {{-- CORREO --}}
                <div class="mb-3">
                    <label class="form-label">Correo</label>
                    <input type="email"
                        name="correo"
                        class="form-control"
                        value="{{ $usuario->correo }}"
                        required>
                </div>

                {{-- ROL --}}
                <div class="mb-3">
                    <label class="form-label">Rol</label>

                    <select name="rol_id" class="form-select" required>

                        @foreach($roles as $rol)

                        <option value="{{ $rol->id }}"
                            {{ $usuario->rol_id == $rol->id ? 'selected' : '' }}>

                            {{ $rol->nombre }}

                        </option>

                        @endforeach

                    </select>
                </div>

                {{-- UNIDAD --}}
                <div class="mb-3">
                    <label class="form-label">Unidad</label>

                    <select name="unidad_id" class="form-select">

                        <option value="">Sin unidad</option>

                        @foreach($unidades as $unidad)

                        <option value="{{ $unidad->id }}"
                            {{ $usuario->unidad_id == $unidad->id ? 'selected' : '' }}>

                            {{ $unidad->nombre }}

                        </option>

                        @endforeach

                    </select>
                </div>

                {{-- CONTRASEÑA NUEVA --}}
<div class="mb-3">
    <label class="form-label">Nueva contraseña</label>
    <input type="password"
        name="contrasena"
        class="form-control"
        placeholder="Dejar vacío si no se desea cambiar">
</div>

{{-- CONFIRMAR CONTRASEÑA --}}
<div class="mb-3">
    <label class="form-label">Confirmar contraseña</label>
    <input type="password"
        name="contrasena_confirmation"
        class="form-control">
</div>

                <div class="d-flex justify-content-end gap-2">

                    <a href="/usuarios" class="btn btn-secondary">
                        Cancelar
                    </a>

                    <button class="btn btn-primary">
                        Guardar Cambios
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>

@endsection