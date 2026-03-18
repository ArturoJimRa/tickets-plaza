@extends('layouts.app')

@section('contenido')
    <h1>Dashboard</h1>

    <p>Rol ID: {{ session('rol') }}</p>

    <ul>
        <li>✔ Sistema funcionando</li>
        <li>✔ Login validado</li>
        <li>✔ Middleware activo</li>
    </ul>
@endsection
