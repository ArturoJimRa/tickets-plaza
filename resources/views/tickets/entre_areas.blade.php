@extends('layouts.app')

@section('title', 'Tickets entre áreas')

@section('content')

<div class="container mt-4">

    <h4 class="fw-bold mb-3">🔄 Tickets entre áreas</h4>

    <div class="card shadow-sm">
        <div class="card-body">

            {{-- 🔎 FILTROS (opcional, ya listo para después) --}}
            <form method="GET" class="row g-2 mb-3">

                <div class="col-md-3">
                    <input type="date" name="fecha_inicio" class="form-control"
                        value="{{ request('fecha_inicio') }}">
                </div>

                <div class="col-md-3">
                    <input type="date" name="fecha_fin" class="form-control"
                        value="{{ request('fecha_fin') }}">
                </div>

                <div class="col-md-3">
                    <button class="btn btn-primary w-100">Filtrar</button>
                </div>

            </form>

            @if($tickets->isEmpty())
                <div class="alert alert-secondary">
                    No hay tickets entre áreas.
                </div>
            @else

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Área origen</th>
                            <th>Área destino</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acción</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($tickets as $t)
                        <tr>
                            <td>{{ $t->id }}</td>

                            <td>
                                <strong>{{ $t->titulo }}</strong>
                            </td>

                            <td>
                                <span class="badge bg-secondary">
                                    {{ $t->area_origen }}
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ $t->area_destino }}
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-warning text-dark">
                                    {{ $t->estado }}
                                </span>
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($t->fecha_creacion)->format('d/m/Y H:i') }}
                            </td>

                            <td>
                                <a href="{{ url('/tickets/' . $t->id) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    Ver
                                </a>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

            @endif

        </div>
    </div>

</div>

@endsection