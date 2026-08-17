@extends('layouts.app')

@section('title', 'Detalle del Ticket')

@section('content')

{{-- ===============================
   ENCABEZADO
=============================== --}}
<div class="d-flex justify-content-between align-items-center mb-4">

    <h4 class="mb-0">
        🎫 Ticket: <span class="text-primary">{{ $ticket->folio ?? '#'.$ticket->id }}</span>
    </h4>

    <a href="/tickets" class="btn btn-outline-secondary btn-sm">
        ⬅ Volver
    </a>

</div>

{{-- MENSAJES --}}
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

{{-- ===============================
   INFO DEL TICKET
=============================== --}}
<div class="card shadow-sm mb-4">

    <div class="card-header bg-dark text-white">
        Información del ticket
    </div>

    <div class="card-body">

        <div class="row mb-3">

            <div class="col-md-4">
                <strong>Folio:</strong>
                <span class="fw-bold text-primary">{{ $ticket->folio ?? '#'.$ticket->id }}</span>
            </div>

            <div class="col-md-4">
                <strong>Título:</strong>
                {{ $ticket->titulo }}
            </div>

            <div class="col-md-4">

                <strong>Estado:</strong>

                @if($ticket->estado === 'Cerrado')
                    <span class="badge bg-success">Cerrado</span>

                @elseif($ticket->estado === 'En proceso')
                    <span class="badge bg-info text-dark">En proceso</span>

                @elseif($ticket->estado === 'Resuelto')
                    <span class="badge bg-primary">Resuelto</span>

                @else
                    <span class="badge bg-warning text-dark">Abierto</span>
                @endif

            </div>

        </div>

        <p>
            <strong>Descripción:</strong><br>
            {{ $ticket->descripcion }}
        </p>

        <div class="row">

            <div class="col-md-4">
                <strong>Unidad:</strong>
                {{ $ticket->unidad }}
            </div>

            <div class="col-md-4">
                <strong>Categoría:</strong>
                {{ $ticket->categoria }}
            </div>

            @if($ticket->subcategoria)
            <div class="col-md-4">
                <strong>Subcategoría:</strong>
                {{ $ticket->subcategoria }}
            </div>
            @endif

        </div>

        <hr>

        <div class="row">

            <div class="col-md-4">
                <strong>Creado por:</strong>
                {{ $ticket->creador }}
            </div>

            <div class="col-md-4">
                <strong>Asignado a:</strong>
                {{ $ticket->asignado_a ?? 'Sin asignar' }}
            </div>

            <div class="col-md-4">
                <strong>Fecha creación:</strong>
                {{ $ticket->fecha_creacion }}
            </div>

        </div>

        {{-- PRIORIDAD --}}
        @if($ticket->prioridad)

        <hr>

        <div class="row">

            <div class="col-md-4">

                <strong>Prioridad:</strong><br>

                <span class="badge
                    @if($ticket->prioridad == 'critico') bg-danger
                    @elseif($ticket->prioridad == 'alto') bg-warning
                    @elseif($ticket->prioridad == 'medio') bg-primary
                    @else bg-secondary
                    @endif">

                    {{ ucfirst($ticket->prioridad) }}

                </span>

            </div>

            @if($ticket->sla_horas)
            <div class="col-md-4">
                <strong>SLA:</strong><br>
                {{ $ticket->sla_horas }} horas
            </div>
            @endif

            @if($ticket->fecha_limite)
            <div class="col-md-4">
                <strong>Fecha límite:</strong><br>
                {{ $ticket->fecha_limite }}
            </div>
            @endif

        </div>

        @endif

    </div>

</div>

{{-- ===============================
   RESPUESTAS
=============================== --}}
<div class="card shadow-sm mb-4">

    <div class="card-header bg-secondary text-white">
        Respuestas
    </div>

    <div class="card-body">

        @if($respuestas->isEmpty())

            <p class="text-muted">
                No hay respuestas aún.
            </p>

        @else

            @foreach($respuestas as $r)

                <div class="border rounded p-3 mb-3">

                    <strong>{{ $r->nombre }}</strong>

                    <p class="mb-1">
                        {{ $r->mensaje }}
                    </p>

                    <small class="text-muted">
                        {{ $r->fecha }}
                    </small>

                </div>

            @endforeach

        @endif

    </div>

</div>

{{-- =========================================================
   FORMULARIO ÚNICO DE GESTIÓN
========================================================= --}}
@if(
    $ticket->estado !== 'Cerrado'
    &&
    (
        session('rol') === 'Admin'
        ||
        (
            session('es_jefe')
            &&
            session('rol_id') == $ticket->rol_destino_id
        )
        ||
        session('usuario_id') == $ticket->asignado_id
    )
)

<div class="card shadow-sm mb-4">

    <div class="card-header
        @if(session('rol') === 'Admin')
            bg-danger
        @elseif(session('es_jefe'))
            bg-primary
        @else
            bg-success
        @endif
        text-white">

        @if(session('rol') === 'Admin')
            Administración del Ticket
        @elseif(session('es_jefe'))
            Gestión del Ticket
        @else
            Actualizar Ticket
        @endif

    </div>

    <div class="card-body">

        <form method="POST" action="/tickets/{{ $ticket->id }}/responder">
            @csrf

            {{-- =====================================================
                ASIGNACIÓN
                ADMIN Y JEFE PUEDEN ASIGNAR
                TRABAJADOR SOLO REASIGNA
            ====================================================== --}}
            <div class="mb-3">

                <label class="form-label">

                    @if(
                        session('rol') === 'Admin'
                        ||
                        session('es_jefe')
                    )
                        Asignar a
                    @else
                        Reasignar a
                    @endif

                </label>

                <select name="asignado_a" class="form-select">

                    <option value="">
                        @if(session('usuario_id') == $ticket->asignado_id)
                            Mantener asignación
                        @else
                            Seleccione personal
                        @endif
                    </option>

                    @foreach($usuariosSistemas as $usuario)

                        <option value="{{ $usuario->id }}"
                            {{ $ticket->asignado_id == $usuario->id ? 'selected' : '' }}>

                            {{ $usuario->nombre }}

                        </option>

                    @endforeach

                </select>

            </div>

            {{-- =====================================================
                PRIORIDAD
                ADMIN: SIEMPRE PUEDE CAMBIAR
                JEFE: SOLO SI NO EXISTE PRIORIDAD
            ====================================================== --}}
            @if(session('rol') === 'Admin')

                <div class="mb-3">

                    <label class="form-label">
                        Prioridad
                    </label>

                    <select name="prioridad" class="form-select">

                        <option value="">
                            Seleccione prioridad
                        </option>

                        <option value="critico"
                            {{ $ticket->prioridad == 'critico' ? 'selected' : '' }}>
                            🔴 Crítico
                        </option>

                        <option value="alto"
                            {{ $ticket->prioridad == 'alto' ? 'selected' : '' }}>
                            🟠 Alto
                        </option>

                        <option value="medio"
                            {{ $ticket->prioridad == 'medio' ? 'selected' : '' }}>
                            🟡 Medio
                        </option>

                        <option value="bajo"
                            {{ $ticket->prioridad == 'bajo' ? 'selected' : '' }}>
                            🟢 Bajo
                        </option>

                    </select>

                </div>

            @elseif(session('es_jefe'))

                @if(!$ticket->prioridad)

                    <div class="mb-3">

                        <label class="form-label">
                            Prioridad
                        </label>

                        <select name="prioridad" class="form-select">

                            <option value="">
                                Seleccione prioridad
                            </option>

                            <option value="critico">🔴 Crítico</option>
                            <option value="alto">🟠 Alto</option>
                            <option value="medio">🟡 Medio</option>
                            <option value="bajo">🟢 Bajo</option>

                        </select>

                    </div>

                @else

                    <div class="mb-3">

                        <label class="form-label">
                            Prioridad
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ ucfirst($ticket->prioridad) }}"
                            disabled>

                        <small class="text-muted">
                            La prioridad ya fue definida
                        </small>

                    </div>

                @endif

            @endif

            {{-- RESPUESTA --}}
            <div class="mb-3">

                <label class="form-label">
                    Respuesta
                </label>

                <textarea
                    name="mensaje"
                    class="form-control"
                    rows="4"
                    required></textarea>

            </div>

            {{-- ESTADO --}}
            <div class="mb-3">

                <label class="form-label">
                    Cambiar estado
                </label>

                <select
                    name="estado_ticket_id"
                    class="form-select"
                    required>

                    @foreach($estados as $estado)

                        <option value="{{ $estado->id }}"
                            {{ $ticket->estado === $estado->nombre ? 'selected' : '' }}>

                            {{ $estado->nombre }}

                        </option>

                    @endforeach

                </select>

            </div>

            <button class="btn
                @if(session('rol') === 'Admin')
                    btn-danger
                @elseif(session('es_jefe'))
                    btn-primary
                @else
                    btn-success
                @endif">

                Guardar cambios

            </button>

        </form>

    </div>

</div>

@endif

{{-- =========================================================
   USUARIO UNIDAD → SOLO VISUALIZA
========================================================= --}}
@if(session('rol') === 'Unidad')

<div class="alert alert-info shadow-sm">
    Este ticket es solo de visualización.
</div>

@endif

@endsection