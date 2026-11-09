@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 720px">
    <h1 class="h3 mb-1">Registrar devolución</h1>
    <p class="text-muted mb-3">
        <span class="font-monospace">{{ $asignacion->producto->referencia }}</span> · {{ $asignacion->producto->nombre }}
    </p>

    <div class="alert alert-primary">
        👤 Lo tiene <strong>{{ $asignacion->trabajador->nombre_completo }}</strong>
        desde el {{ $asignacion->fecha_recogida->format('d/m/Y') }}
        ({{ $asignacion->tipo === 'permanente' ? 'permanente' : 'préstamo' }}).
        @if ($asignacion->observaciones)
            <div class="small mt-1">Notas: {{ $asignacion->observaciones }}</div>
        @endif
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('asignaciones.devolver.store', $asignacion) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="fecha_entrega" class="form-label">Fecha de entrega *</label>
                        <input type="date" name="fecha_entrega" id="fecha_entrega"
                               class="form-control @error('fecha_entrega') is-invalid @enderror"
                               value="{{ old('fecha_entrega', now()->format('Y-m-d')) }}"
                               min="{{ $asignacion->fecha_recogida->format('Y-m-d') }}"
                               max="{{ now()->format('Y-m-d') }}" required>
                        @error('fecha_entrega') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sede_devolucion_id" class="form-label">¿En qué sede lo deja? *</label>
                        <select name="sede_devolucion_id" id="sede_devolucion_id"
                                class="form-select @error('sede_devolucion_id') is-invalid @enderror" required>
                            @foreach ($sedes as $sede)
                                <option value="{{ $sede->id }}"
                                        @selected(old('sede_devolucion_id', $asignacion->producto->sede_id) == $sede->id)>
                                    {{ $sede->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('sede_devolucion_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label d-block">¿Cómo vuelve? *</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="estado" id="estado_disponible" value="disponible"
                               @checked(old('estado', 'disponible') === 'disponible')>
                        <label class="form-check-label" for="estado_disponible">Bien, queda disponible</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="estado" id="estado_reparacion" value="reparacion"
                               @checked(old('estado') === 'reparacion')>
                        <label class="form-check-label" for="estado_reparacion">Con avería, a reparación</label>
                    </div>
                    @error('estado') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label for="observaciones" class="form-label">Observaciones de la devolución</label>
                    <textarea name="observaciones" id="observaciones" rows="2"
                              class="form-control @error('observaciones') is-invalid @enderror"
                              placeholder="Ej.: falta el cable HDMI">{{ old('observaciones') }}</textarea>
                    @error('observaciones') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button class="btn btn-success">Registrar devolución</button>
                <a href="{{ route('productos.show', $asignacion->producto) }}" class="btn btn-link">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection