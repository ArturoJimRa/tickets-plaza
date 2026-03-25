@extends('layouts.app')

@section('title', 'Subcategorías')

@section('content')

<div class="container">

    <h4 class="mb-3">📂 Subcategorías</h4>

    <a href="/admin/subcategorias/create" class="btn btn-primary mb-3">
        ➕ Nueva Subcategoría
    </a>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subcategorias as $sub)
                        <tr>
                            <td>{{ $sub->id }}</td>
                            <td>{{ $sub->nombre }}</td>
                            <td>{{ $sub->categoria->nombre ?? 'Sin categoría' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">
                                No hay subcategorías registradas
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

</div>

@endsection