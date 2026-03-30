<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login | Sistema Tickets</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container vh-100 d-flex justify-content-center align-items-center">

    <div class="col-md-4">
        <div class="card shadow-lg border-0">

            <div class="card-header bg-dark text-white text-center">
                <h5 class="mb-0">Sistema de Tickets ISCR</h5>
                <small>Grupo Plaza</small>
            </div>

            <div class="card-body p-4">

                {{-- MENSAJE DE ERROR --}}
                @if(session('error'))
                    <div class="alert alert-danger text-center">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="/login">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email"
                               name="correo"
                               class="form-control"
                               placeholder="Ingresa tu correo electrónico"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password"
                               name="contrasena"
                               class="form-control"
                               placeholder="Ingresa tu contraseña"
                               required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-dark">
                            Iniciar sesión
                        </button>
                    </div>
                </form>

            </div>

            <div class="card-footer text-center text-muted small">
                © {{ date('Y') }} Grupo Plaza   v4.1
            </div>

        </div>
    </div>

</div>

</body>
</html>
