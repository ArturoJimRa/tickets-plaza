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
        <div class="card-body p-0">

            <table class="table table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>Categoría Relacionada</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($subcategorias as $sub)
                        <tr>
                            <td>{{ $sub->nombre }}</td>

                            <td>
                                {{ $sub->categoria->nombre ?? 'Sin categoría' }}
                            </td>

                            <td class="text-center">

                                {{-- EDITAR --}}
                                <a href="/admin/subcategorias/{{ $sub->id }}/edit"
                                   class="btn btn-sm btn-warning">
                                    ✏️ Editar
                                </a>

                            </td>
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