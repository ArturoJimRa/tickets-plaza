@extends('layouts.app')

@section('title', 'Categorías de Ticket')

@section('content')

<h4 class="mb-4">📂 Categorías de Ticket</h4>

<a href="/admin/categorias/create" class="btn btn-primary mb-3">
    + Nueva Categoría
</a>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
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
                            <span class="badge bg-success">
                                {{ ucfirst($c->estado) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            No hay categorías registradas
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>

    </div>
</div>

@endsection