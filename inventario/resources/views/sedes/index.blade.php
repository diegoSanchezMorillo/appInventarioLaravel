@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Sedes</h1>
        <a href="{{ route('sedes.create') }}" class="btn btn-primary">Nueva sede</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Localidad</th>
                        <th>Dirección</th>
                        <th class="text-center">Productos</th>
                        <th class="text-center">Trabajadores</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sedes as $sede)
                        <tr>
                            <td class="fw-semibold">{{ $sede->nombre }}</td>
                            <td>{{ $sede->localidad ?? '—' }}</td>
                            <td>{{ $sede->direccion ?? '—' }}</td>
                            <td class="text-center">{{ $sede->productos_count }}</td>
                            <td class="text-center">{{ $sede->trabajadores_count }}</td>
                            <td class="text-center">
                                @if ($sede->activa)
                                    <span class="badge bg-success">Activa</span>
                                @else
                                    <span class="badge bg-secondary">Inactiva</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('sedes.edit', $sede) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                <form action="{{ route('sedes.destroy', $sede) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar la sede {{ $sede->nombre }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No hay sedes registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $sedes->links() }}
    </div>
</div>
@endsection