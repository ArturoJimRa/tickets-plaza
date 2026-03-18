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
