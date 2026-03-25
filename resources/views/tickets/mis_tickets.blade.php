@extends('layouts.app')

@section('title', 'Mis Tickets Asignados')

@section('content')

@php
    \Carbon\Carbon::setLocale('es');
@endphp

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
                        <th>Prioridad</th>
                        <th>Tiempo restante</th>
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

                        <td>
                            <span class="badge bg-secondary">
                                {{ $t->categoria }}
                            </span>
                        </td>

                        {{-- ESTADO --}}
                        <td>
                            @if($t->estado === 'Cerrado')
                                <span class="badge bg-success">Cerrado</span>
                            @elseif($t->estado === 'En proceso')
                                <span class="badge bg-info text-dark">En proceso</span>
                            @elseif($t->estado === 'Resuelto')
                                <span class="badge bg-primary">Resuelto</span>
                            @else
                                <span class="badge bg-warning text-dark">Abierto</span>
                            @endif
                        </td>

                        {{-- PRIORIDAD --}}
                        <td>
                            @if($t->prioridad)
                                @switch($t->prioridad)
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

                        {{-- 🚦 SLA --}}
                        <td>
                            @if($t->fecha_limite)

                                @php
                                    $ahora = \Carbon\Carbon::now();
                                    $limite = \Carbon\Carbon::parse($t->fecha_limite);
                                    $minutos = $ahora->diffInMinutes($limite, false);
                                @endphp

                                {{-- 🔴 VENCIDO --}}
                                @if($minutos < 0)
                                    <span class="badge bg-danger">
                                        Vencido
                                    </span>

                                {{-- 🟡 POR VENCER --}}
                                @elseif($minutos <= 120)
                                    <span class="badge bg-warning text-dark">
                                        {{ $limite->diffForHumans(null, true, false, 2) }}
                                    </span>

                                {{-- 🟢 EN TIEMPO --}}
                                @else
                                    <span class="badge bg-success">
                                        {{ $limite->diffForHumans(null, true, false, 2) }}
                                    </span>
                                @endif

                            @else
                                <span class="text-muted">Sin SLA</span>
                            @endif
                        </td>

                        {{-- FECHA --}}
                        <td>
                            {{ \Carbon\Carbon::parse($t->fecha_creacion)->format('d/m/Y H:i') }}
                        </td>

                        {{-- ACCIÓN --}}
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