@extends('layouts.app')

@section('title', 'Crear Ticket')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-8">

        <div class="card shadow-sm border-0">

            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">🎫 Crear nuevo ticket</h5>
            </div>

            <div class="card-body">

                {{-- MENSAJES DE ERROR --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="/tickets">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text"
                               name="titulo"
                               class="form-control"
                               placeholder="Ej. Problema con impresora"
                               value="{{ old('titulo') }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion"
                                  class="form-control"
                                  rows="4"
                                  placeholder="Describe detalladamente el problema"
                                  required>{{ old('descripcion') }}</textarea>
                    </div>

                    <div class="alert alert-info">
                        <strong>Unidad:</strong> {{ session('unidad_id') ?? 'Asignada automáticamente' }}
                    </div>

                    {{-- 🔥 ÁREA --}}
                    <div class="mb-3">
                        <label class="form-label">Área</label>
                        <select name="rol_destino_id" id="area" class="form-select" required>
                            <option value="">Seleccione un área</option>
                            @foreach($roles as $rol)
                                @if($rol->nombre !== 'Admin' && $rol->nombre !== 'Unidad')
                                    <option value="{{ $rol->id }}"
                                        {{ old('rol_destino_id') == $rol->id ? 'selected' : '' }}>
                                        {{ $rol->nombre }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    {{-- 🔥 CATEGORÍA --}}
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select name="categoria_id" id="categoria" class="form-select" required>
                            <option value="">Primero selecciona un área</option>
                        </select>
                    </div>

                    {{-- 🔥 SUBCATEGORÍA --}}
                    <div class="mb-3" id="contenedor-subcategoria" style="display: none;">
                        <label class="form-label">Subcategoría</label>
                        <select name="subcategoria_id" id="subcategoria" class="form-select">
                            <option value="">Seleccione una subcategoría</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="/tickets" class="btn btn-secondary">
                            ⬅ Volver
                        </a>

                        <button type="submit" class="btn btn-primary">
                            Crear ticket
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

@endsection


<script>
document.addEventListener('DOMContentLoaded', function () {

    const areaSelect = document.getElementById('area');
    const categoriaSelect = document.getElementById('categoria');
    const subcategoriaSelect = document.getElementById('subcategoria');
    const contenedor = document.getElementById('contenedor-subcategoria');

    // 🔥 ÁREA → CATEGORÍAS
    areaSelect.addEventListener('change', function () {

        const areaId = this.value;

        categoriaSelect.innerHTML = '<option>Cargando...</option>';
        subcategoriaSelect.innerHTML = '<option value="">Seleccione una subcategoría</option>';
        contenedor.style.display = 'none';

        if (!areaId) {
            categoriaSelect.innerHTML = '<option value="">Primero selecciona un área</option>';
            return;
        }

        fetch(`/categorias/${areaId}`)
            .then(response => {
                if (!response.ok) throw new Error('Error en servidor');
                return response.json();
            })
            .then(data => {

                categoriaSelect.innerHTML = '<option value="">Seleccione una categoría</option>';

                data.forEach(cat => {
                    categoriaSelect.innerHTML += `
                        <option value="${cat.id}">${cat.nombre}</option>
                    `;
                });

            })
            .catch(error => {
                console.error('Error:', error);
                categoriaSelect.innerHTML = '<option value="">Error al cargar</option>';
            });

    });


    // 🔥 CATEGORÍA → SUBCATEGORÍAS
    categoriaSelect.addEventListener('change', function () {

        const categoriaId = this.value;

        if (!categoriaId) {
            contenedor.style.display = 'none';
            subcategoriaSelect.innerHTML = '<option value="">Seleccione una subcategoría</option>';
            return;
        }

        contenedor.style.display = 'block';
        subcategoriaSelect.innerHTML = '<option value="">Cargando...</option>';

        fetch(`/subcategorias/${categoriaId}`)
            .then(response => {
                if (!response.ok) throw new Error('Error en servidor');
                return response.json();
            })
            .then(data => {

                if (data.length === 0) {
                    contenedor.style.display = 'none';
                    subcategoriaSelect.innerHTML = '<option value="">Sin subcategorías</option>';
                    return;
                }

                subcategoriaSelect.innerHTML = '<option value="">Seleccione una subcategoría</option>';

                data.forEach(sub => {
                    subcategoriaSelect.innerHTML += `
                        <option value="${sub.id}">${sub.nombre}</option>
                    `;
                });

            })
            .catch(error => {
                console.error('Error:', error);
                contenedor.style.display = 'none';
                subcategoriaSelect.innerHTML = '<option value="">Error al cargar</option>';
            });

    });

});
</script>