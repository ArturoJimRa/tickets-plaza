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

                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select name="categoria_id" class="form-select" required>
                            <option value="">Seleccione una categoría</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}"
                                    {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

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

    const categoriaSelect = document.querySelector('select[name="categoria_id"]');
    const subcategoriaSelect = document.getElementById('subcategoria');
    const contenedor = document.getElementById('contenedor-subcategoria');

    categoriaSelect.addEventListener('change', function () {

        const categoriaId = this.value;

        // 🔴 Si no hay categoría seleccionada
        if (!categoriaId) {
            contenedor.style.display = 'none';
            subcategoriaSelect.innerHTML = '<option value="">Seleccione una subcategoría</option>';
            return;
        }

        // Mostrar mientras carga
        contenedor.style.display = 'block';
        subcategoriaSelect.innerHTML = '<option value="">Cargando...</option>';

        fetch(`/admin/subcategorias/${categoriaId}`)
            .then(response => response.json())
            .then(data => {

                // 🔴 Si NO hay subcategorías
                if (data.length === 0) {
                    contenedor.style.display = 'none';
                    subcategoriaSelect.innerHTML = '<option value="">Sin subcategorías</option>';
                    return;
                }

                // 🟢 Si SÍ hay subcategorías
                contenedor.style.display = 'block';
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