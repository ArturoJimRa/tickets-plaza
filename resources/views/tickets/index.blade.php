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

{{-- 🔎 FILTROS COMPLETOS --}}
<form method="GET" class="mb-4">
    <div class="row g-2">

        {{-- BUSCADOR --}}
        <div class="col-md-3">
            <input type="text" name="buscar" class="form-control"
                   placeholder="Buscar..."
                   value="{{ request('buscar') }}">
        </div>

        {{-- FECHA INICIO --}}
        <div class="col-md-2">
            <input type="date" name="fecha_inicio" class="form-control"
                   value="{{ request('fecha_inicio') }}">
        </div>

        {{-- FECHA FIN --}}
        <div class="col-md-2">
            <input type="date" name="fecha_fin" class="form-control"
                   value="{{ request('fecha_fin') }}">
        </div>

        {{-- ESTADO --}}
        <div class="col-md-2">
            <select name="estado" class="form-select">
                <option value="">Estado</option>
                <option value="1" {{ request('estado')=='1'?'selected':'' }}>Abierto</option>
                <option value="2" {{ request('estado')=='2'?'selected':'' }}>En proceso</option>
                <option value="3" {{ request('estado')=='3'?'selected':'' }}>Resuelto</option>
                <option value="4" {{ request('estado')=='4'?'selected':'' }}>Cerrado</option>
            </select>
        </div>

        {{-- PRIORIDAD --}}
        <div class="col-md-2">
            <select name="prioridad" class="form-select">
                <option value="">Prioridad</option>
                <option value="critico" {{ request('prioridad')=='critico'?'selected':'' }}>Crítico</option>
                <option value="alto" {{ request('prioridad')=='alto'?'selected':'' }}>Alto</option>
                <option value="medio" {{ request('prioridad')=='medio'?'selected':'' }}>Medio</option>
                <option value="bajo" {{ request('prioridad')=='bajo'?'selected':'' }}>Bajo</option>
            </select>
        </div>

        {{-- AREA --}}
        <div class="col-md-2">
            <select name="area_id" class="form-select">
                <option value="">Área</option>
                @foreach($areas as $area)
                    <option value="{{ $area->id }}" {{ request('area_id')==$area->id?'selected':'' }}>
                    {{ $area->nombre }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- BOTONES --}}
        <div class="col-md-1 d-grid">
            <button class="btn btn-dark">Buscar</button>
        </div>

    </div>

    {{-- LIMPIAR --}}
    <div class="mt-2">
        <a href="/tickets" class="btn btn-outline-secondary btn-sm">
            Limpiar filtros
        </a>
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
                    <th>Área</th>
                    <th>Categoría</th>
                    <th>Atendiendo</th>
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
                            @if($ticket->area)
                                <span class="badge bg-dark">
                                    {{ $ticket->area }}
                                </span>
                            @else
                                <span class="text-muted">Sin área</span>
                            @endif
                        </td>

                        <td>
                            <span class="badge bg-secondary">
                                {{ $ticket->categoria }}
                            </span>
                        </td>

                        <td>
                            @if($ticket->asignado_a)
                                <span class="badge bg-primary">
                                    {{ $ticket->asignado_a }}
                                </span>
                            @else
                                <span class="text-muted">Sin asignar</span>
                            @endif
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
                                    <span class="badge bg-secondary">{{ $ticket->estado }}</span>
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

                        {{-- TIEMPO --}}
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

                        <td class="text-end">
                            <a href="/tickets/{{ $ticket->id }}" class="btn btn-sm btn-outline-primary">
                                Ver detalle
                            </a>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="11" class="text-center py-4 text-muted">
                            No hay tickets registrados
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>

    </div>
</div>

{{-- 🔥 EXPORTAR SOLO ADMIN --}}
@if(session('rol') === 'Admin')
<a href="{{ url('/tickets/exportar') . '?' . http_build_query(request()->all()) }}"
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
@endif

@endsection