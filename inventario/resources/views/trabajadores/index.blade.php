@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Trabajadores</h1>
        <a href="{{ route('trabajadores.create') }}" class="btn btn-primary">Nuevo trabajador</a>
    </div>

    <form method="GET" action="{{ route('trabajadores.index') }}" class="card card-body mb-3">
        <div class="row g-2">
            <div class="col-md-6">
                <input type="search" name="buscar" class="form-control"
                       placeholder="Buscar por nombre, apellidos o DNI"
                       value="{{ request('buscar') }}">
            </div>
            <div class="col-md-4">
                <select name="sede_id" class="form-select">
                    <option value="">Todas las sedes</option>
                    @foreach ($sedes as $sede)
                        <option value="{{ $sede->id }}" @selected(request('sede_id') == $sede->id)>
                            {{ $sede->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-secondary flex-fill">Filtrar</button>
                @if (request()->hasAny(['buscar', 'sede_id']))
                    <a href="{{ route('trabajadores.index') }}" class="btn btn-outline-secondary" title="Limpiar filtros">✕</a>
                @endif
            </div>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Apellidos, nombre</th>
                        <th>DNI</th>
                        <th>Sede</th>
                        <th class="text-center">Productos en su poder</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($trabajadores as $trabajador)
                        <tr>
                            <td>
                                <a href="{{ route('trabajadores.show', $trabajador) }}" class="fw-semibold text-decoration-none">
                                    {{ $trabajador->apellidos }}, {{ $trabajador->nombre }}
                                </a>
                            </td>
                            <td class="font-monospace">{{ $trabajador->dni ?? '—' }}</td>
                            <td>{{ $trabajador->sede?->nombre ?? '—' }}</td>
                            <td class="text-center">
                                @if ($trabajador->asignaciones_activas_count > 0)
                                    <span class="badge bg-primary">{{ $trabajador->asignaciones_activas_count }}</span>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($trabajador->activo)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('trabajadores.show', $trabajador) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                                <a href="{{ route('trabajadores.edit', $trabajador) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No se han encontrado trabajadores.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $trabajadores->links() }}
    </div>
</div>
@endsection
