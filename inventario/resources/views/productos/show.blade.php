@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <div class="font-monospace text-muted">{{ $producto->referencia }}</div>
            <h1 class="h3 mb-1">{{ $producto->nombre }}</h1>
            <span class="badge bg-{{ $producto->estado_color }}">{{ $producto->estado_texto }}</span>
            <span class="badge bg-light text-dark border">{{ $producto->categoria->nombre }}</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('productos.edit', $producto) }}" class="btn btn-outline-primary">Editar</a>
            <form action="{{ route('productos.destroy', $producto) }}" method="POST"
                  onsubmit="return confirm('¿Eliminar {{ $producto->referencia }}? Solo para altas hechas por error.')">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger">Eliminar</button>
            </form>
        </div>
    </div>

    {{-- Ubicación: lo primero que se ve --}}
    @if ($producto->esta_con_trabajador)
        @php $asignacion = $producto->asignacionActiva; @endphp
        <div class="alert alert-primary d-flex justify-content-between align-items-center">
            <div>
                <div class="small text-uppercase fw-semibold opacity-75">Ubicación actual</div>
                <div class="fs-4">
                    👤
                    <a href="{{ route('trabajadores.show', $asignacion->trabajador) }}" class="alert-link">
                        {{ $asignacion->trabajador->nombre_completo }}
                    </a>
                </div>
                <div>
                    {{ $asignacion->tipo === 'permanente' ? 'Asignación permanente' : 'Préstamo' }}
                    desde el {{ $asignacion->fecha_recogida->format('d/m/Y') }}
                    ({{ $asignacion->fecha_recogida->diffForHumans() }})
                </div>
            </div>
            <a href="{{ route('asignaciones.devolver', $asignacion) }}" class="btn btn-light">
                Registrar devolución
            </a>
        </div>
    @else
        <div class="alert alert-success d-flex justify-content-between align-items-center">
            <div>
                <div class="small text-uppercase fw-semibold opacity-75">Ubicación actual</div>
                <div class="fs-4">🏢 {{ $producto->sede->nombre }}</div>
                @if ($producto->sede->localidad)
                    <div>{{ $producto->sede->localidad }}</div>
                @endif
            </div>
            @if ($producto->estado === 'disponible')
                <a href="{{ route('productos.asignar', $producto) }}" class="btn btn-light">
                    Asignar a trabajador
                </a>
            @else
                <span class="small">No asignable: {{ $producto->estado_texto }}</span>
            @endif
        </div>
    @endif

    <div class="row g-3">
        {{-- Datos --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header fw-semibold">Datos del producto</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><small class="text-muted d-block">Marca / modelo</small>{{ trim("{$producto->marca} {$producto->modelo}") ?: '—' }}</li>
                    <li class="list-group-item"><small class="text-muted d-block">Nº de serie</small><span class="font-monospace">{{ $producto->numero_serie ?? '—' }}</span></li>
                    <li class="list-group-item"><small class="text-muted d-block">Sede de referencia</small>{{ $producto->sede->nombre }}</li>
                    <li class="list-group-item"><small class="text-muted d-block">Fecha de alta</small>{{ $producto->fecha_alta?->format('d/m/Y') ?? '—' }}</li>
                    @if ($producto->descripcion)
                        <li class="list-group-item"><small class="text-muted d-block">Descripción</small>{{ $producto->descripcion }}</li>
                    @endif
                </ul>
            </div>
        </div>

        {{-- Historial --}}
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header fw-semibold">Historial de asignaciones</div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Trabajador</th>
                                <th>Tipo</th>
                                <th>Recogida</th>
                                <th>Entrega</th>
                                <th>Devuelto en</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($historial as $registro)
                                <tr @class(['table-primary' => is_null($registro->fecha_entrega)])>
                                    <td>
                                        {{ $registro->trabajador->nombre_completo }}
                                        @if ($registro->trabajador->trashed())
                                            <small class="text-muted">(baja)</small>
                                        @endif
                                    </td>
                                    <td>{{ $registro->tipo === 'permanente' ? 'Permanente' : 'Préstamo' }}</td>
                                    <td>{{ $registro->fecha_recogida->format('d/m/Y') }}</td>
                                    <td>
                                        @if ($registro->fecha_entrega)
                                            {{ $registro->fecha_entrega->format('d/m/Y') }}
                                        @else
                                            <span class="badge bg-primary">En su poder</span>
                                        @endif
                                    </td>
                                    <td>{{ $registro->sedeDevolucion?->nombre ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Este producto nunca se ha asignado.</td>
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
