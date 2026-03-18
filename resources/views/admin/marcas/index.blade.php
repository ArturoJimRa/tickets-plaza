@extends('layouts.app')

@section('title', 'Marcas')

@section('content')

<h4 class="mb-4">🏷️ Marcas</h4>

<a href="{{ route('marcas.create') }}" class="btn btn-primary mb-3">
    + Nueva Marca
</a>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-hover">
    <thead class="table-dark">
        <tr>
            <th>Nombre</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($marcas as $marca)
            <tr>
                <td>{{ $marca->nombre }}</td>
                <td>
                    <span class="badge {{ $marca->estado == 'activo' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($marca->estado) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('marcas.edit', $marca) }}" class="btn btn-sm btn-warning">
                        Editar
                    </a>

                    <form action="{{ route('marcas.destroy', $marca) }}"
                          method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger"
                            onclick="return confirm('¿Cambiar estado de la marca?')">
                            Cambiar estado
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@endsection