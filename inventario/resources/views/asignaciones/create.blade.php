@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 720px">
    <h1 class="h3 mb-1">Asignar producto</h1>
    <p class="text-muted mb-3">
        <span class="font-monospace">{{ $producto->referencia }}</span> · {{ $producto->nombre }}
        · ahora en 🏢 {{ $producto->sede->nombre }}
    </p>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('productos.asignar.store', $producto) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="trabajador_id" class="form-label">Trabajador *</label>
                    <select name="trabajador_id" id="trabajador_id"
                            class="form-select @error('trabajador_id') is-invalid @enderror" required>
                        <option value="">— Elige un trabajador —</option>
                        @foreach ($trabajadores as $trabajador)
                            <option value="{{ $trabajador->id }}" @selected(old('trabajador_id') == $trabajador->id)>
                                {{ $trabajador->apellidos }}, {{ $trabajador->nombre }}
                                @if ($trabajador->sede) — {{ $trabajador->sede->nombre }} @endif
                            </option>
                        @endforeach
                    </select>
                    @error('trabajador_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label d-block">Tipo *</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tipo" id="tipo_temporal" value="temporal"
                                   @checked(old('tipo', 'temporal') === 'temporal')>
                            <label class="form-check-label" for="tipo_temporal">Préstamo temporal</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tipo" id="tipo_permanente" value="permanente"
                                   @checked(old('tipo') === 'permanente')>
                            <label class="form-check-label" for="tipo_permanente">Permanente</label>
                        </div>
                        @error('tipo') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="fecha_recogida" class="form-label">Fecha de recogida *</label>
                        <input type="date" name="fecha_recogida" id="fecha_recogida"
                               class="form-control @error('fecha_recogida') is-invalid @enderror"
                               value="{{ old('fecha_recogida', now()->format('Y-m-d')) }}"
                               max="{{ now()->format('Y-m-d') }}" required>
                        @error('fecha_recogida') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="observaciones" class="form-label">Observaciones</label>
                    <textarea name="observaciones" id="observaciones" rows="2"
                              class="form-control @error('observaciones') is-invalid @enderror"
                              placeholder="Ej.: se lleva también el cargador y la funda">{{ old('observaciones') }}</textarea>
                    @error('observaciones') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button class="btn btn-primary">Asignar</button>
                <a href="{{ route('productos.show', $producto) }}" class="btn btn-link">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection