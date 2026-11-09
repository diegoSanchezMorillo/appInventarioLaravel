@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h1 class="h3 mb-1">{{ $trabajador->nombre_completo }}</h1>
            <div class="text-muted">
                {{ $trabajador->sede?->nombre ?? 'Sin sede asignada' }}
                @unless ($trabajador->activo)
                    · <span class="badge bg-secondary">Inactivo</span>
                @endunless
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('trabajadores.edit', $trabajador) }}" class="btn btn-outline-primary">Editar</a>
            <form action="{{ route('trabajadores.destroy', $trabajador) }}" method="POST"
                  onsubmit="return confirm('¿Dar de baja a {{ $trabajador->nombre_completo }}? Su historial se conservará.')">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger">Dar de baja</button>
            </form>
        </div>
    </div>

    <div class="row g-3">
        {{-- Datos de contacto --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header fw-semibold">Datos</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><small class="text-muted d-block">DNI / NIE</small>{{ $trabajador->dni ?? '—' }}</li>
                    <li class="list-group-item"><small class="text-muted d-block">Email</small>{{ $trabajador->email ?? '—' }}</li>
                    <li class="list-group-item"><small class="text-muted d-block">Teléfono</small>{{ $trabajador->telefono ?? '—' }}</li>
                </ul>
            </div>
        </div>

        {{-- Productos en su poder --}}
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header fw-semibold">
                    Productos en su poder
                    <span class="badge bg-primary ms-1">{{ $trabajador->asignacionesActivas->count() }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Referencia</th>
                                <th>Producto</th>
                                <th>Tipo</th>
                                <th>Desde</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($trabajador->asignacionesActivas as $asignacion)
                                <tr>
                                    <td class="font-monospace">{{ $asignacion->producto->referencia }}</td>
                                    <td>
                                        {{ $asignacion->producto->nombre }}
                                        <small class="text-muted d-block">{{ $asignacion->producto->categoria->nombre }}</small>
                                    </td>
                                    <td>
                                        @if ($asignacion->tipo === 'permanente')
                                            <span class="badge bg-info text-dark">Permanente</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Préstamo</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $asignacion->fecha_recogida->format('d/m/Y') }}
                                        <small class="text-muted d-block">{{ $asignacion->fecha_recogida->diffForHumans() }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No tiene ningún producto ahora mismo.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Historial --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header fw-semibold">Historial de devoluciones</div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Referencia</th>
                                <th>Producto</th>
                                <th>Recogida</th>
                                <th>Entrega</th>
                                <th>Tipo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($historial as $asignacion)
                                <tr>
                                    <td class="font-monospace">{{ $asignacion->producto->referencia }}</td>
                                    <td>{{ $asignacion->producto->nombre }}</td>
                                    <td>{{ $asignacion->fecha_recogida->format('d/m/Y') }}</td>
                                    <td>{{ $asignacion->fecha_entrega->format('d/m/Y') }}</td>
                                    <td>{{ $asignacion->tipo === 'permanente' ? 'Permanente' : 'Préstamo' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">Sin devoluciones registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
