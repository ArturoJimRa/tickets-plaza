@extends('layouts.app')

@section('title', 'Categorías de Ticket')

@section('content')

<div class="container mt-4">

    <h4 class="mb-4">📂 Categorías de Ticket</h4>

    <a href="/admin/categorias/create" class="btn btn-primary mb-3">
        + Nueva Categoría
    </a>

    {{-- MENSAJE DE ÉXITO --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- MENSAJE DE ERROR --}}
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">

            <table class="table table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Área que atiende</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($categorias as $c)
                        <tr>
                            <td>{{ $c->nombre }}</td>

                            <td>{{ $c->descripcion }}</td>

                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ $c->rol_destino }}
                                </span>
                            </td>

                            <td>
                                @if($c->estado == 'activo')
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>

                            {{-- ACCIONES --}}
                            <td class="text-center">

                                {{-- EDITAR --}}
                                <a href="/admin/categorias/{{ $c->id }}/edit"
                                   class="btn btn-sm btn-warning">
                                    ✏️ Editar
                                </a>

                                {{-- ACTIVAR / DESACTIVAR --}}
                                <form action="/admin/categorias/{{ $c->id }}" 
                                      method="POST" 
                                      style="display:inline;">
                                    @csrf
                                    @method('DELETE')

                                    @if($c->estado == 'activo')
                                        <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('¿Desactivar esta categoría?')">
                                            ❌ Desactivar
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-success"
                                                onclick="return confirm('¿Activar esta categoría?')">
                                            ✅ Activar
                                        </button>
                                    @endif
                                </form>

                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                No hay categorías registradas
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>

@endsection