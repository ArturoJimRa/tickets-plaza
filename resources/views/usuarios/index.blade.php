@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold">Gestión de Usuarios</h3>

    <a href="/usuarios/create" class="btn btn-primary">
        + Nuevo usuario
    </a>
</div>

@if(session('ok'))
    <div class="alert alert-success">
        {{ session('ok') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-body p-0">

        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Unidad</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>

                @forelse($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->id }}</td>

                    <td class="fw-semibold">
                        {{ $usuario->nombre }}
                    </td>

                    <td>{{ $usuario->correo }}</td>

                    <td>
                        <span class="badge bg-info text-dark">
                            {{ $usuario->rol }}
                        </span>
                    </td>

                    <td>
                        {{ $usuario->unidad ?? '—' }}
                    </td>

                    <td>
                        @if($usuario->estado === 'activo')
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-secondary">Inactivo</span>
                        @endif
                    </td>

                    <td class="text-center">

                        {{-- BOTON EDITAR --}}
                        <a href="{{ route('usuarios.edit', $usuario->id) }}" 
                            class="btn btn-sm btn-primary">
                            Editar
                        </a>

                        <form method="POST" action="/usuarios/{{ $usuario->id }}/estado" class="d-inline">
                            @csrf
                            <button class="btn btn-sm {{ $usuario->estado === 'activo' ? 'btn-warning' : 'btn-success' }}"
                                onclick="return confirm('¿Seguro que deseas cambiar el estado del usuario?')">
                                {{ $usuario->estado === 'activo' ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        No hay usuarios registrados
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>

    </div>
</div>

@endsection
