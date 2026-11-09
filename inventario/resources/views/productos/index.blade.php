@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Productos</h1>
        <a href="{{ route('productos.create') }}" class="btn btn-primary">Nuevo producto</a>
    </div>

    {{-- Buscador y filtros --}}
    <form method="GET" action="{{ route('productos.index') }}" class="card card-body mb-3">
        <div class="row g-2">
            <div class="col-lg-4">
                <input type="search" name="buscar" class="form-control" autofocus
                       placeholder="Referencia, nombre, nº de serie, marca..."
                       value="{{ request('buscar') }}">
            </div>
            <div class="col-lg-3">
                <select name="ubicacion" class="form-select">
                    <option value="">¿Dónde está? (todo)</option>
                    <optgroup label="En sede">
                        @foreach ($sedes as $sede)
                            <option value="s-{{ $sede->id }}" @selected(request('ubicacion') === "s-{$sede->id}")>
                                {{ $sede->nombre }}
                            </option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Con trabajador">
                        @foreach ($trabajadores as $trabajador)
                            <option value="t-{{ $trabajador->id }}" @selected(request('ubicacion') === "t-{$trabajador->id}")>
                                {{ $trabajador->apellidos }}, {{ $trabajador->nombre }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>
            </div>
            <div class="col-lg-2">
                <select name="categoria_id" class="form-select">
                    <option value="">Todas las categorías</option>
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->id }}" @selected(request('categoria_id') == $categoria->id)>
                            {{ $categoria->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2">
                <select name="estado" class="form-select">
                    <option value="">Todos los estados</option>
                    @foreach (\App\Models\Producto::ESTADOS as $valor => $texto)
                        <option value="{{ $valor }}" @selected(request('estado') === $valor)>{{ $texto }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-1 d-flex gap-2">
                <button class="btn btn-secondary flex-fill" title="Buscar">🔍</button>
                @if (request()->hasAny(['buscar', 'ubicacion', 'categoria_id', 'estado']))
                    <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary" title="Limpiar filtros">✕</a>
                @endif
            </div>
        </div>
    </form>

    <div class="text-muted small mb-2">{{ $productos->total() }} producto(s)</div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Referencia</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Ubicación actual</th>
                        <th>Estado</th>
                        <th class="text-end"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($productos as $producto)
                        <tr>
                            <td>
                                <a href="{{ route('productos.show', $producto) }}" class="font-monospace fw-semibold text-decoration-none">
                                    {{ $producto->referencia }}
                                </a>
                            </td>
                            <td>
                                {{ $producto->nombre }}
                                @if ($producto->marca || $producto->modelo)
                                    <small class="text-muted d-block">{{ trim("{$producto->marca} {$producto->modelo}") }}</small>
                                @endif
                            </td>
                            <td>{{ $producto->categoria->nombre }}</td>
                            <td>
                                @if ($producto->esta_con_trabajador)
                                    <span class="badge rounded-pill text-bg-primary">👤 {{ $producto->ubicacion }}</span>
                                    <small class="text-muted d-block">
                                        desde {{ $producto->asignacionActiva->fecha_recogida->format('d/m/Y') }}
                                    </small>
                                @else
                                    <span class="badge rounded-pill text-bg-success">🏢 {{ $producto->ubicacion }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $producto->estado_color }}">{{ $producto->estado_texto }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('productos.show', $producto) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No se han encontrado productos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $productos->links() }}
    </div>
</div>
@endsection