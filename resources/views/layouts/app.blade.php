<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Sistema Tickets Grupo Plaza')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">

        <a class="navbar-brand" href="/dashboard">
            🎫 Sistema Tickets
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">

            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                {{-- ADMIN --}}
                @if(session('rol') === 'Admin')
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">Dashboard</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/usuarios">Usuarios</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/admin/roles">Roles</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/admin/marcas">Marcas</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/admin/unidades">Unidades</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/admin/categorias">Categorías</a>
                    </li>

                    </li class="nav-item">
                        <a class="nav-link" href="/admin/subcategorias">Subcategorias</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/tickets">Tickets</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/tickets/create">Crear ticket</a>
                    </li>
                @endif

                {{-- SISTEMAS --}}
                @if(session('rol') !== 'Admin' && session('rol') !== 'Unidad')
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">Dashboard</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/tickets/create">Crear ticket</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/mis-tickets">Mis tickets</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/tickets">Todos los tickets</a>
                    </li>

                    <li>
                        <a class="nav-link" href="/tickets/entre-areas">Tickets entre áreas</a>
                    </li>
                @endif

                {{-- UNIDAD --}}
                @if(session('rol') === 'Unidad')
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">Dashboard</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/tickets/create">Crear ticket</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/tickets">Mis tickets</a>
                    </li>
                @endif

            </ul>

            <span class="navbar-text text-white me-3">
                {{ session('usuario_nombre') ?? '' }}
            </span>

           <a href="/cambiar-password" class="btn btn-warning btn-sm me-2">
                Cambiar contraseña
            </a>

            <a href="{{ route('logout') }}" class="btn btn-danger btn-sm">
                Cerrar sesión
            </a>
        </div>
    </div>
</nav>

<div class="container mt-4">

    {{-- MENSAJES --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
