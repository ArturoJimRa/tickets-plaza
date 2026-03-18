@extends('layouts.app')

@section('title', 'Unidades')

@section('content')
<div class="container mt-4">

    {{-- TÍTULO + BOTÓN --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">🏢 Unidades</h4>

        <a href="{{ route('unidades.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nueva Unidad
        </a>
    </div>

    {{-- MENSAJE SUCCESS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- TABLA --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Unidad</th>
                        <th>Marca</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($unidades as $unidad)
                        <tr>
                            <td class="fw-semibold">
                                {{ $unidad->nombre }}
                            </td>

                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ $unidad->marca->nombre }}
                                </span>
                            </td>

                            <td class="text-center">
                                @if($unidad->estado === 'activo')
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <a href="{{ route('unidades.edit', $unidad) }}"
                                   class="btn btn-sm btn-warning">
                                    ✏️ Editar
                                </a>

                                <form action="{{ route('unidades.estado', $unidad) }}"
                                      method="POST"
                                      class="d-inline">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-sm {{ $unidad->estado === 'activo' ? 'btn-danger' : 'btn-success' }}">
                                        {{ $unidad->estado === 'activo' ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                No hay unidades registradas
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>
</div>
@endsection