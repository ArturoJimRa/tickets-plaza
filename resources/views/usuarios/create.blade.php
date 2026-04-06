@extends('layouts.app')

@section('title', 'Alta de Usuario')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-6">

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Alta de Usuario</h5>
            </div>

            <div class="card-body">

                {{-- MENSAJES DE ERROR --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Error:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- MENSAJE DE ÉXITO --}}
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="/usuarios">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Nombre completo</label>
                        <input type="text"
                               name="nombre"
                               class="form-control"
                               value="{{ old('nombre') }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email"
                               name="correo"
                               class="form-control"
                               value="{{ old('correo') }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password"
                               name="contrasena"
                               class="form-control"
                               required>
                    </div>

                    {{-- 🔥 ROL --}}
                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select name="rol_id" id="rolSelect" class="form-select" required>
                            <option value="">Seleccione rol</option>
                            @foreach(DB::table('roles')->get() as $rol)
                                <option value="{{ $rol->id }}"
                                        data-nombre="{{ $rol->nombre }}"
                                    {{ old('rol_id') == $rol->id ? 'selected' : '' }}>
                                    {{ $rol->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 🔥 JEFE --}}
                    <div class="mb-3" id="jefeContainer">
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="es_jefe"
                                   value="1"
                                   id="esJefeCheck"
                                   {{ old('es_jefe') ? 'checked' : '' }}>
                            <label class="form-check-label" for="esJefeCheck">
                                ¿Es jefe de área?
                            </label>
                        </div>
                    </div>

                    {{-- 🔥 UNIDAD --}}
                    <div class="mb-4" id="unidadContainer">
                        <label class="form-label">Unidad</label>
                        <select name="unidad_id" class="form-select">
                            <option value="">Sin unidad</option>
                            @foreach(DB::table('unidades')->where('estado','activo')->get() as $unidad)
                                <option value="{{ $unidad->id }}"
                                    {{ old('unidad_id') == $unidad->id ? 'selected' : '' }}>
                                    {{ $unidad->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="/usuarios" class="btn btn-secondary">
                            ← Volver
                        </a>

                        <button type="submit" class="btn btn-success">
                            Crear usuario
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

{{-- 🔥 SCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const rolSelect = document.getElementById('rolSelect');
    const unidadContainer = document.getElementById('unidadContainer');
    const jefeContainer = document.getElementById('jefeContainer');

    function toggleCampos() {
        const selectedOption = rolSelect.options[rolSelect.selectedIndex];
        const rolNombre = selectedOption.getAttribute('data-nombre');

        // 🏢 Mostrar unidad solo si es "Unidad"
        if (rolNombre === 'Unidad') {
            unidadContainer.style.display = 'block';
            jefeContainer.style.display = 'none'; // ❌ Unidad no tiene jefe
        } else {
            unidadContainer.style.display = 'none';
            jefeContainer.style.display = 'block';
        }
    }

    toggleCampos();
    rolSelect.addEventListener('change', toggleCampos);
});
</script>

@endsection