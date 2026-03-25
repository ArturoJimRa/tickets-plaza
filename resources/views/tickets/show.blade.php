@extends('layouts.app')

@section('title', 'Detalle del Ticket')

@section('content')

{{-- ===============================
   ENCABEZADO
=============================== --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">🎫 Ticket #{{ $ticket->id }}</h4>

    <a href="/tickets" class="btn btn-outline-secondary btn-sm">
        ⬅ Volver
    </a>
</div>

{{-- ===============================
   MENSAJES
=============================== --}}
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
        <div class="row mb-2">
            <div class="col-md-6"><strong>Título:</strong> {{ $ticket->titulo }}</div>
            <div class="col-md-6">
                <strong>Estado:</strong>

                @if($ticket->estado === 'Cerrado')
                    <span class="badge bg-success">Cerrado</span>
                @elseif($ticket->estado === 'En proceso')
                    <span class="badge bg-info text-dark">En proceso</span>
                @elseif($ticket->estado === 'Resuelto')
                    <span class="badge bg-primary text-dark">Resuelto</span>
                @else
                    <span class="badge bg-warning text-dark">Abierto</span>
                @endif
            </div>
        </div>

        <p><strong>Descripción:</strong><br>{{ $ticket->descripcion }}</p>

        <div class="row">
            <div class="col-md-4"><strong>Unidad:</strong> {{ $ticket->unidad }}</div>
            <div class="col-md-4"><strong>Categoría:</strong> {{ $ticket->categoria }}</div>
            <div class="col-md-4"><strong>Creado por:</strong> {{ $ticket->creador }}</div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-6">
                <strong>Asignado a:</strong>
                {{ $ticket->asignado_a ?? 'Sin asignar' }}
            </div>
            <div class="col-md-6">
                <strong>Fecha creación:</strong>
                {{ $ticket->fecha_creacion }}
            </div>
        </div>

        {{-- ===============================
           🔥 SLA INFO (NUEVO)
        =============================== --}}
        @if($ticket->sla_horas)
        <hr>

        <h6 class="fw-bold">⏱ Información del SLA</h6>

        <div class="row">

            {{-- PRIORIDAD --}}
            <div class="col-md-3">
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

            {{-- SLA HORAS --}}
            <div class="col-md-3">
                <strong>SLA:</strong><br>
                {{ $ticket->sla_horas }} horas
            </div>

            {{-- FECHA LÍMITE --}}
            <div class="col-md-3">
                <strong>Fecha límite:</strong><br>
                {{ $ticket->fecha_limite ?? 'Sin definir' }}
            </div>

            {{-- TIEMPO RESTANTE --}}
            <div class="col-md-3">
                <strong>Tiempo restante:</strong><br>

                @if($ticket->fecha_limite)
                    @php
                        $ahora = \Carbon\Carbon::now();
                        $limite = \Carbon\Carbon::parse($ticket->fecha_limite);
                        $diff = $ahora->diff($limite);

                        $horas = ($diff->days * 24) + $diff->h;
                        $minutos = $diff->i;
                    @endphp

                    {{ $horas }}h {{ $minutos }}m
                @else
                    N/A
                @endif

            </div>

        </div>
        @endif

        @if($ticket->estado === 'Cerrado')
        <div class="alert alert-secondary mt-3">
            <strong>📄 Ticket cerrado</strong><br>

            <strong>Fecha de cierre:</strong> {{ $ticket->fecha_cierre }} <br>

            <strong>Cerrado por:</strong>
            {{ $ticket->cerrado_por_nombre }}
        </div>
        @endif
    </div>
</div>

{{-- ===============================
   ASIGNAR TICKET
=============================== --}}
@if (
    $ticket->estado !== 'Cerrado' &&
    (
        session('rol') === 'Admin' ||
        session('rol_id') == $ticket->rol_destino_id
    )
)

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        Asignar ticket
    </div>

    <div class="card-body">
        <form method="POST" action="/tickets/{{ $ticket->id }}/asignar">
            @csrf

            <div class="mb-3">
                <select name="asignado_a" class="form-select" required>
                    <option value="">Seleccione personal del área</option>
                    @foreach($usuariosSistemas as $usuario)
                        <option value="{{ $usuario->id }}"
                            {{ $ticket->asignado_id == $usuario->id ? 'selected' : '' }}>
                            {{ $usuario->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

        <div class="mb-3">
    <label>Prioridad</label>
    <select name="prioridad" class="form-select" required>
        <option value="">Seleccione</option>
        <option value="critico">🔴 Crítico</option>
        <option value="alto">🟠 Alto</option>
        <option value="medio">🟡 Medio</option>
        <option value="bajo">🟢 Bajo</option>
    </select>
    </div>

    <div class="mb-3">
    <label>Tiempo de solución (horas)</label>
    <input type="number" name="sla_horas" class="form-control" min="1" required>
    </div>

            <button class="btn btn-primary">Asignar</button>
        </form>
    </div>
</div>

@endif

{{-- ===============================
   RESPUESTAS
=============================== --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-secondary text-white">
        Respuestas
    </div>

    <div class="card-body">
        @if ($respuestas->isEmpty())
            <p class="text-muted">No hay respuestas aún.</p>
        @else
            @foreach ($respuestas as $r)
                <div class="border rounded p-3 mb-3">
                    <strong>{{ $r->nombre }}</strong>
                    <p class="mb-1">{{ $r->mensaje }}</p>
                    <small class="text-muted">{{ $r->fecha }}</small>
                </div>
            @endforeach
        @endif
    </div>
</div>

{{-- ===============================
   RESPONDER / CERRAR
=============================== --}}
@if (
    $ticket->estado !== 'Cerrado' &&
    (
        session('rol') === 'Admin' ||
        session('rol_id') == $ticket->rol_destino_id ||
        session('usuario_id') == $ticket->asignado_id
    )
)

<div class="card shadow-sm">
    <div class="card-header bg-success text-white">
        Responder ticket
    </div>

    <div class="card-body">
        <form method="POST" action="/tickets/{{ $ticket->id }}/responder">
            @csrf

            <div class="mb-3">
                <textarea name="mensaje" class="form-control" rows="4" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Cambiar estado</label>
                <select name="estado_ticket_id" class="form-select" required>
                    @foreach ($estados as $estado)
                        <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <button class="btn btn-success">Enviar respuesta</button>
        </form>

        <hr>

        <form method="POST" action="/tickets/{{ $ticket->id }}/cerrar"
              onsubmit="return confirm('¿Seguro que deseas cerrar este ticket?')">
            @csrf
            <button class="btn btn-danger">Cerrar ticket</button>
        </form>
    </div>
</div>

@endif

@endsection