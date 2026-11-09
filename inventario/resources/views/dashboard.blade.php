@extends('layouts.app')

@php
    $tarjetas = [
        'disponible' => ['texto' => 'Disponibles',   'color' => 'success'],
        'asignado'   => ['texto' => 'Asignados',     'color' => 'primary'],
        'reparacion' => ['texto' => 'En reparación', 'color' => 'warning'],
        'baja'       => ['texto' => 'De baja',       'color' => 'secondary'],
    ];
@endphp

@section('content')
<div class="container">
    <h1 class="h3 mb-3">Inicio</h1>

    {{-- Totales por estado --}}
    <div class="row g-3 mb-4">
        @foreach ($tarjetas as $estado => $tarjeta)
            <div class="col-6 col-lg-3">
                <a href="{{ route('productos.index', ['estado' => $estado]) }}" class="text-decoration-none">
                    <div class="card border-{{ $tarjeta['color'] }} border-start border-4 h-100">
                        <div class="card-body">
                            <div class="text-muted small text-uppercase">{{ $tarjeta['texto'] }}</div>
                            <div class="display-6 fw-semibold text-{{ $tarjeta['color'] }}">
                                {{ $porEstado[$estado] ?? 0 }}
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        {{-- Préstamos largos --}}
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Préstamos de más de {{ $diasAlerta }} días</span>
                    @if ($prestamosLargos->isNotEmpty())
                        <span class="badge bg-danger">{{ $prestamosLargos->count() }}</span>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Trabajador</th>
                                <th class="text-end">Días</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($prestamosLargos as $prestamo)
                                <tr>
                                    <td>
                                        <a href="{{ route('productos.show', $prestamo->producto) }}" class="font-monospace text-decoration-none">
                                            {{ $prestamo->producto->referencia }}
                                        </a>
                                        <small class="text-muted d-block">{{ $prestamo->producto->nombre }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('trabajadores.show', $prestamo->trabajador) }}" class="text-decoration-none">
                                            {{ $prestamo->trabajador->nombre_completo }}
                                        </a>
                                        <small class="text-muted d-block">desde {{ $prestamo->fecha_recogida->format('d/m/Y') }}</small>
                                    </td>
                                    <td class="text-end">
                                        {{-- Más del doble del límite: en rojo --}}
                                        <span class="badge {{ $prestamo->dias >= $diasAlerta * 2 ? 'bg-danger' : 'bg-warning text-dark' }}">
                                            {{ $prestamo->dias }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('asignaciones.devolver', $prestamo) }}" class="btn btn-sm btn-outline-success">Devolver</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        ✅ No hay préstamos pendientes de más de {{ $diasAlerta }} días.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Por sede --}}
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header fw-semibold">Por sede</div>
                <ul class="list-group list-group-flush">
                    @foreach ($sedes as $sede)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">🏢 {{ $sede->nombre }}</div>
                                @if ($sede->localidad)
                                    <small class="text-muted">{{ $sede->localidad }}</small>
                                @endif
                            </div>
                            <div class="text-end">
                                <a href="{{ route('productos.index', ['ubicacion' => 's-' . $sede->id]) }}"
                                   class="badge rounded-pill text-bg-success text-decoration-none">
                                    {{ $sede->en_sede_count }} aquí
                                </a>
                                @if ($sede->fuera_count > 0)
                                    <span class="badge rounded-pill text-bg-primary">{{ $sede->fuera_count }} prestados</span>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Por categoría --}}
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header fw-semibold">Por categoría</div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Categoría</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Disponibles</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categorias as $categoria)
                                <tr>
                                    <td>
                                        <a href="{{ route('productos.index', ['categoria_id' => $categoria->id]) }}" class="text-decoration-none">
                                            {{ $categoria->nombre }}
                                        </a>
                                    </td>
                                    <td class="text-center">{{ $categoria->total_count }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('productos.index', ['categoria_id' => $categoria->id, 'estado' => 'disponible']) }}"
                                           @class([
                                               'text-decoration-none fw-semibold',
                                               'text-success' => $categoria->disponibles_count > 0,
                                               'text-danger'  => $categoria->disponibles_count === 0,
                                           ])>
                                            {{ $categoria->disponibles_count }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Últimos movimientos --}}
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header fw-semibold">Últimos movimientos</div>
                <ul class="list-group list-group-flush">
                    @forelse ($movimientos as $movimiento)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    @if ($movimiento->fecha_entrega)
                                        <span class="badge bg-success">Devuelto</span>
                                    @else
                                        <span class="badge bg-primary">Entregado</span>
                                    @endif
                                    <a href="{{ route('productos.show', $movimiento->producto) }}" class="font-monospace text-decoration-none ms-1">
                                        {{ $movimiento->producto->referencia }}
                                    </a>
                                    {{ $movimiento->fecha_entrega ? 'por' : 'a' }}
                                    {{ $movimiento->trabajador->nombre_completo }}
                                </div>
                                <small class="text-muted text-nowrap ms-2">{{ $movimiento->updated_at->diffForHumans() }}</small>
                            </div>
                            @if ($movimiento->usuario)
                                <small class="text-muted">Registrado por {{ $movimiento->usuario->name }}</small>
                            @endif
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted py-4">Todavía no hay movimientos.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
