@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')


    {{-- 🔔 HEADER CON NOTIFICACIONES --}}
    @php
    $notificaciones = DB::table('notificaciones')
        ->where('usuario_id', session('usuario_id'))
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    $noLeidas = DB::table('notificaciones')
        ->where('usuario_id', session('usuario_id'))
        ->where('leida', 0)
        ->count();
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>
            <h4 class="mb-1">👋 Bienvenido, {{ session('nombre') }}</h4>
            <p class="text-muted mb-0">
                Rol: <strong>{{ session('rol') }}</strong>
            </p>
        </div>

         {{-- 🔔 NOTIFICACIONES --}}
        @php
        $notificaciones = DB::table('notificaciones')
            ->where('usuario_id', session('usuario_id'))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $noLeidas = DB::table('notificaciones')
            ->where('usuario_id', session('usuario_id'))
            ->where('leida', 0)
            ->count();
        @endphp

        <div class="dropdown">

            <button
                class="btn btn-sm position-relative shadow-sm rounded-circle notification-btn {{ $noLeidas > 0 ? 'btn-danger notification-alert' : 'btn-outline-secondary' }}"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
                id="btnNotificaciones"
                style="width:45px;height:45px;"
            >
                🔔

                @if($noLeidas > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-light text-danger border">
                    {{ $noLeidas }}
                </span>
                @endif
            </button>

            <div class="dropdown-menu dropdown-menu-end p-0 shadow border-0"
                 style="width: 380px; max-height: 500px; overflow-y: auto;">

                {{-- HEADER --}}
                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom bg-light">

                    <strong>Notificaciones</strong>

                    <div class="d-flex gap-1">

                        {{-- LEER TODAS --}}
                        <form method="POST" action="/notificaciones/leer-todas">
                            @csrf
                            <button class="btn btn-sm btn-light border">
                                ✔
                            </button>
                        </form>

                        {{-- ELIMINAR TODAS --}}
                        <form method="POST" action="/notificaciones/eliminar-todas">
                            @csrf
                            <button class="btn btn-sm btn-light border text-danger">
                                🗑
                            </button>
                        </form>

                    </div>

                </div>

                {{-- LISTADO --}}
                @forelse($notificaciones as $n)

                <div class="px-3 py-2 border-bottom small {{ !$n->leida ? 'bg-light' : '' }}">

                    <div class="d-flex justify-content-between align-items-start">

                        <div style="width:85%;">

                            <div class="fw-semibold">
                                {{ $n->titulo }}
                            </div>

                            <div class="text-muted small">
                                {{ $n->mensaje }}
                            </div>

                            <div class="text-secondary" style="font-size:11px;">
                                {{ \Carbon\Carbon::parse($n->created_at)->diffForHumans() }}
                            </div>

                        </div>

                        <div class="d-flex flex-column gap-1">

                            {{-- LEÍDA --}}
                            @if(!$n->leida)
                            <form method="POST" action="/notificaciones/{{ $n->id }}/leer">
                                @csrf
                                <button class="btn btn-sm btn-light border py-0 px-1">
                                    ✔
                                </button>
                            </form>
                            @endif

                            {{-- ELIMINAR --}}
                            <form method="POST" action="/notificaciones/{{ $n->id }}">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm btn-light border text-danger py-0 px-1">
                                    ✕
                                </button>
                            </form>

                        </div>

                    </div>

                </div>

                @empty

                <div class="p-3 text-center text-muted small">
                    No tienes notificaciones
                </div>

                @endforelse

            </div>
        </div>

    </div>

    {{-- =======================
        ADMIN
    ======================= --}}
    @if(session('rol') === 'Admin')
    <div class="row g-3">

        {{-- USUARIOS --}}
        <div class="col-md-4">
            <div class="card text-bg-primary h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">👤 Usuarios</h5>
                    <p class="card-text">Administrar usuarios del sistema</p>
                    <a href="/usuarios" class="btn btn-light btn-sm">Gestionar</a>
                </div>
            </div>
        </div>

        {{-- ROLES --}}
        <div class="col-md-4">
            <div class="card text-bg-secondary h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">🧩 Roles</h5>
                    <p class="card-text">Áreas del sistema (Sistemas, Marketing, etc.)</p>
                    <a href="/admin/roles" class="btn btn-light btn-sm">Gestionar</a>
                </div>
            </div>
        </div>

        {{-- MARCAS --}}
        <div class="col-md-4">
            <div class="card text-bg-dark h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">🏷️ Marcas</h5>
                    <p class="card-text">Administrar marcas registradas</p>
                    <a href="/admin/marcas" class="btn btn-light btn-sm">Gestionar</a>
                </div>
            </div>
        </div>

        {{-- UNIDADES --}}
        <div class="col-md-4">
            <div class="card text-bg-info h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">🏢 Unidades</h5>
                    <p class="card-text">Unidades asociadas a marcas</p>
                    <a href="{{ route('unidades.index') }}" class="btn btn-dark btn-sm">Gestionar</a>
                </div>
            </div>
        </div>

        {{-- CATEGORÍAS --}}
        <div class="col-md-4">
            <div class="card text-bg-warning h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">📂 Categorías</h5>
                    <p class="card-text">Categorías de tickets por área</p>
                    <a href="/admin/categorias" class="btn btn-dark btn-sm">Gestionar</a>
                </div>
            </div>
        </div>

        {{-- SUBCATEGORÍAS --}}
        <div class="col-md-4">
            <div class="card bg-danger-subtle h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">📂 Subcategorías</h5>
                    <p class="card-text">Subcategorías por categoria creada</p>
                    <a href="/admin/subcategorias" class="btn btn-dark btn-sm">Gestionar</a>
                </div>
            </div>
        </div>

        {{-- TICKETS --}}
        <div class="col-md-4">
            <div class="card text-bg-white h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">🎫 Tickets</h5>
                    <p class="card-text">Ver y supervisar todos los tickets</p>
                    <a href="/tickets" class="btn btn-light btn-sm">Ver tickets</a>
                </div>
            </div>
        </div>

        {{-- CREAR TICKET --}}
        <div class="col-md-4">
            <div class="card text-bg-success h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">➕ Crear ticket</h5>
                    <p class="card-text">Reportar una incidencia</p>
                    <a href="/tickets/create" class="btn btn-light btn-sm">Nuevo ticket</a>
                </div>
            </div>
        </div>

    </div>
    @endif

    {{-- =======================
    ROLES QUE ATIENDEN TICKETS
    ======================= --}}
    @if(session('rol') !== 'Admin' && session('rol') !== 'Unidad')
    <div class="row g-3">

        <div class="col-md-4">
            <div class="card text-bg-success h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">➕ Crear ticket</h5>
                    <p class="card-text">Reportar una incidencia</p>
                    <a href="/tickets/create" class="btn btn-light btn-sm">Nuevo ticket</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-bg-warning h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">🛠️ Mis tickets</h5>
                    <p class="card-text">Tickets asignados a ti</p>
                    <a href="/mis-tickets" class="btn btn-dark btn-sm">Ver</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-bg-secondary h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">📜 Historial</h5>
                    <p class="card-text">Todos los tickets del sistema</p>
                    <a href="/tickets" class="btn btn-light btn-sm">Ver</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-bg-primary h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Tickets entre áreas</h5>
                    <p class="card-text">Tickets dirigidos a otras áreas</p>
                    <a href="/tickets/entre-areas" class="btn btn-light btn-sm">Ver</a>
                </div>
            </div>
        </div>

    </div>
    @endif

    {{-- =======================
        UNIDAD
    ======================= --}}
    @if(session('rol') === 'Unidad')
    <div class="row g-3">

        <div class="col-md-4">
            <div class="card text-bg-success h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">➕ Crear ticket</h5>
                    <p class="card-text">Reportar una incidencia</p>
                    <a href="/tickets/create" class="btn btn-light btn-sm">Nuevo ticket</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-bg-info h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">📋 Mis tickets</h5>
                    <p class="card-text">Seguimiento de incidencias</p>
                    <a href="/tickets" class="btn btn-dark btn-sm">Ver</a>
                </div>
            </div>
        </div>

    </div>
    @endif

</div>
@endsection