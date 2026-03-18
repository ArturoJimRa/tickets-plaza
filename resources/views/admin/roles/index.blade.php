@extends('layouts.app')

@section('title', 'Roles')

@section('content')

<h4 class="mb-4">👥 Roles del Sistema</h4>

<a href="/admin/roles/create" class="btn btn-primary mb-3">
    + Nuevo Rol
</a>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-hover">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
        </tr>
    </thead>
    <tbody>
        @foreach($roles as $r)
            <tr>
                <td>{{ $r->id }}</td>
                <td>{{ $r->nombre }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@endsection