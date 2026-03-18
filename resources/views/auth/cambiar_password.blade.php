@extends('layouts.app')

@section('title', 'Mis Tickets Asignados')

@section('content')
<h2>Cambiar contraseña</h2>

@if(session('error'))
    <p style="color:red">{{ session('error') }}</p>
@endif

@if(session('ok'))
    <p style="color:green">{{ session('ok') }}</p>
@endif

<form method="POST" action="/cambiar-password">
    @csrf

    <label>Contraseña actual</label><br>
    <input type="password" name="actual" required><br><br>

    <label>Nueva contraseña</label><br>
    <input type="password" name="nueva" required><br><br>

    <label>Confirmar nueva contraseña</label><br>
    <input type="password" name="confirmacion" required><br><br>

    <button type="submit">Actualizar contraseña</button>
</form>

@endsection