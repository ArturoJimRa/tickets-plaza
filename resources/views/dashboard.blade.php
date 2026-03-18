@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mt-4">

    <h4 class="mb-1">👋 Bienvenido, {{ session('nombre') }}</h4>
    <p class="text-muted mb-4">
        Rol: <strong>{{ session('rol') }}</strong>
    </p>

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

        {{-- TICKETS --}}
        <div class="col-md-4">
            <div class="card text-bg-success h-100 shadow-sm">
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
    (Sistemas, Marketing, RH, etc.)
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