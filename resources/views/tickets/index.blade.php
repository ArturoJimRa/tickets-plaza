@extends('layouts.app')

@section('title', 'Tickets')

@section('content')

@php
    \Carbon\Carbon::setLocale('es');
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">🎫 Lista de Tickets</h4>

    @if(session('rol') === 'Unidad')
        <a href="/tickets/create" class="btn btn-primary">
            + Nuevo Ticket
        </a>
    @endif
</div>

{{-- 🔎 FILTRO --}}
<form method="GET" class="mb-3">
    <div class="input-group">
        <input type="text" name="buscar" class="form-control"
               placeholder="Buscar por ID, título, unidad, categoría o prioridad..."
               value="{{ request('buscar') }}">
        <button class="btn btn-dark">Buscar</button>
    </div>
</form>

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
                    <th>Prioridad</th>
                    <th>Tiempo restante</th>
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

                        {{-- ESTADO --}}
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

                        {{-- PRIORIDAD --}}
                        <td>
                            @if($ticket->prioridad)
                                @switch($ticket->prioridad)
                                    @case('critico')
                                        <span class="badge bg-danger">Crítico</span>
                                        @break
                                    @case('alto')
                                        <span class="badge bg-warning text-dark">Alto</span>
                                        @break
                                    @case('medio')
                                        <span class="badge bg-info text-dark">Medio</span>
                                        @break
                                    @case('bajo')
                                        <span class="badge bg-secondary">Bajo</span>
                                        @break
                                @endswitch
                            @else
                                <span class="text-muted">Sin prioridad</span>
                            @endif
                        </td>

                        {{-- 🚦 TIEMPO RESTANTE --}}
                        <td>
                            @if($ticket->fecha_limite)

                                @php
                                    $limite = \Carbon\Carbon::parse($ticket->fecha_limite);
                                    $ahora = \Carbon\Carbon::now();

                                    $referencia = $ticket->fecha_cierre 
                                        ? \Carbon\Carbon::parse($ticket->fecha_cierre) 
                                        : $ahora;

                                    $minutos = $referencia->diffInMinutes($limite, false);
                                @endphp

                                @if($ticket->estado === 'Cerrado')

                                    @if($minutos < 0)
                                        <span class="badge bg-danger">Fuera de tiempo</span>
                                    @else
                                        <span class="badge bg-success">Completado</span>
                                    @endif

                                @else

                                    @if($minutos < 0)
                                        <span class="badge bg-danger">Vencido</span>
                                    @elseif($minutos <= 120)
                                        <span class="badge bg-warning text-dark">
                                            {{ $limite->diffForHumans(null, true, false, 2) }}
                                        </span>
                                    @else
                                        <span class="badge bg-success">
                                            {{ $limite->diffForHumans(null, true, false, 2) }}
                                        </span>
                                    @endif

                                @endif

                            @else
                                <span class="text-muted">Sin SLA</span>
                            @endif
                        </td>

                        {{-- FECHA --}}
                        <td>
                            {{ \Carbon\Carbon::parse($ticket->fecha_creacion)->format('d/m/Y') }}
                        </td>

                        {{-- ACCIÓN --}}
                        <td class="text-end">
                            <a href="/tickets/{{ $ticket->id }}" class="btn btn-sm btn-outline-primary">
                                Ver detalle
                            </a>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">
                            No hay tickets registrados
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>

    </div>
</div>

{{-- ===============================
   🔥 BOTÓN FLOTANTE EXCEL
=============================== --}}
<a href="/tickets/exportar"
   class="btn btn-success shadow-lg"
   style="
       position: fixed;
       bottom: 25px;
       right: 25px;
       border-radius: 50px;
       padding: 12px 18px;
       z-index: 999;
       font-weight: bold;
   ">
    📊 Exportar a Excel
</a>

@endsection