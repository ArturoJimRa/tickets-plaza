@extends('layouts.app')

@section('title', 'Tickets')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">🎫 Lista de Tickets</h4>

    @if(session('rol') === 'Unidad')
        <a href="/tickets/create" class="btn btn-primary">
            + Nuevo Ticket
        </a>
    @endif
</div>

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
                    <th>ID</th>
                    <th>Título</th>
                    <th>Unidad</th>
                    <th>Categoría</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th class="text-end">Acción</th>
                </tr>
            </thead>
            <tbody>

                @forelse($tickets as $ticket)
                    <tr>
                        <td>#{{ $ticket->id }}</td>

                        <td>
                            <strong>{{ $ticket->titulo }}</strong>
                        </td>

                        <td>{{ $ticket->unidad }}</td>

                        <td>
                            <span class="badge bg-secondary">
                                {{ $ticket->categoria }}
                            </span>
                        </td>

                        <td>
    @switch($ticket->estado)
        @case('Abierto')
            <span class="badge bg-warning text-dark">Abierto</span>
            @break

        @case('En proceso')
            <span class="badge bg-info text-dark">En proceso</span>
            @break

        @case('Resuelto')
            <span class="badge bg-primary">Resuelto</span>
            @break

        @case('Cerrado')
            <span class="badge bg-success">Cerrado</span>
            @break

        @default
            <span class="badge bg-secondary">Desconocido</span>
    @endswitch
</td>


                        <td>
                            {{ \Carbon\Carbon::parse($ticket->fecha_creacion)->format('d/m/Y') }}
                        </td>

                        <td class="text-end">
                            <a href="/tickets/{{ $ticket->id }}" class="btn btn-sm btn-outline-primary">
                                Ver detalle
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            No hay tickets registrados
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>

    </div>
</div>

@endsection
