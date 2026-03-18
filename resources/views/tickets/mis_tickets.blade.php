@extends('layouts.app')

@section('title', 'Mis Tickets Asignados')

@section('content')

{{-- ===============================
   ENCABEZADO
=============================== --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">🎫 Mis tickets asignados</h4>

    <a href="/dashboard" class="btn btn-outline-secondary btn-sm">
        ⬅ Volver
    </a>
</div>

{{-- ===============================
   CONTENIDO
=============================== --}}
@if ($tickets->isEmpty())

    <div class="alert alert-info">
        No tienes tickets asignados actualmente.
    </div>

@else

    <div class="card shadow-sm">
        <div class="card-body p-0">

            <table class="table table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Título</th>
                        <th>Unidad</th>
                        <th>Categoría</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($tickets as $t)
                    <tr>
                        <td>{{ $t->id }}</td>

                        <td>
                            <strong>{{ $t->titulo }}</strong>
                        </td>

                        <td>{{ $t->unidad }}</td>

                        <td>{{ $t->categoria }}</td>

                        <td>
                            @if($t->estado === 'Cerrado')
                                <span class="badge bg-success ">Cerrado</span>
                            @elseif($t->estado === 'En proceso')
                                <span class="badge bg-info text-dark">En proceso</span>
                            @elseif($t->estado === 'Resuelto')
                                <span class="badge bg-primary text-dark">Resuelto</span>
                            @else
                                <span class="badge bg-warning text-dark">Abierto</span>
                            @endif
                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($t->fecha_creacion)->format('d/m/Y H:i') }}
                        </td>

                        <td class="text-center">
                            <a href="/tickets/{{ $t->id }}" class="btn btn-sm btn-primary">
                                Ver
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>

        </div>
    </div>

@endif

@endsection
