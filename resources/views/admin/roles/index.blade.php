@extends('layouts.app')

@section('title', 'Roles')

@section('content')

<div class="container mt-4">

    <h4 class="mb-4">👥 Roles del Sistema</h4>

    <a href="/admin/roles/create" class="btn btn-primary mb-3">
        + Nuevo Rol
    </a>

    {{-- MENSAJE --}}
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
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Prefijo Folio</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($roles as $r)
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td>{{ $r->nombre }}</td>
                            <td>
                                @if($r->prefijo_folio)
                                    <span class="badge bg-primary">{{ $r->prefijo_folio }}</span>
                                @else
                                    <span class="text-muted">Sin prefijo</span>
                                @endif
                            </td>

                            <td class="text-center">
                                {{-- EDITAR --}}
                                <a href="/admin/roles/{{ $r->id }}/edit"
                                   class="btn btn-sm btn-warning">
                                    ✏️ Editar
                                </a>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                No hay roles registrados
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>

@endsection