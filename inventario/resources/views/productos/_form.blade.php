@php
    $editando  = $producto->exists;
    $asignado  = $editando && $producto->asignacionActiva;
@endphp

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="categoria_id" class="form-label">Categoría *</label>
        @if ($editando)
            <input type="text" class="form-control" value="{{ $producto->categoria->nombre }}" disabled>
            <div class="form-text">La categoría no se puede cambiar: de ella depende la referencia <strong>{{ $producto->referencia }}</strong>.</div>
        @else
            <select name="categoria_id" id="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror" required>
                <option value="">— Elige una categoría —</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}" @selected(old('categoria_id') == $categoria->id)>
                        {{ $categoria->nombre }} (siguiente: {{ $categoria->siguiente_referencia }})
                    </option>
                @endforeach
            </select>
            @error('categoria_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        @endif
    </div>
    <div class="col-md-6 mb-3">
        <label for="nombre" class="form-label">Nombre *</label>
        <input type="text" name="nombre" id="nombre"
               class="form-control @error('nombre') is-invalid @enderror"
               value="{{ old('nombre', $producto->nombre) }}" required>
        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="marca" class="form-label">Marca</label>
        <input type="text" name="marca" id="marca"
               class="form-control @error('marca') is-invalid @enderror"
               value="{{ old('marca', $producto->marca) }}">
        @error('marca') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="modelo" class="form-label">Modelo</label>
        <input type="text" name="modelo" id="modelo"
               class="form-control @error('modelo') is-invalid @enderror"
               value="{{ old('modelo', $producto->modelo) }}">
        @error('modelo') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="numero_serie" class="form-label">Nº de serie</label>
        <input type="text" name="numero_serie" id="numero_serie"
               class="form-control font-monospace @error('numero_serie') is-invalid @enderror"
               value="{{ old('numero_serie', $producto->numero_serie) }}">
        @error('numero_serie') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-5 mb-3">
        <label for="sede_id" class="form-label">Sede *</label>
        <select name="sede_id" id="sede_id" class="form-select @error('sede_id') is-invalid @enderror" required>
            <option value="">— Elige una sede —</option>
            @foreach ($sedes as $sede)
                <option value="{{ $sede->id }}" @selected(old('sede_id', $producto->sede_id) == $sede->id)>
                    {{ $sede->nombre }}{{ $sede->activa ? '' : ' (inactiva)' }}
                </option>
            @endforeach
        </select>
        <div class="form-text">Dónde se guarda cuando no lo tiene ningún trabajador.</div>
        @error('sede_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="estado" class="form-label">Estado *</label>
        @if ($asignado)
            <input type="text" class="form-control" value="Asignado" disabled>
            <div class="form-text">Lo tiene {{ $producto->asignacionActiva->trabajador->nombre_completo }}. Registra la devolución para cambiar el estado.</div>
        @else
            <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror" required>
                @foreach (\Illuminate\Support\Arr::except(\App\Models\Producto::ESTADOS, 'asignado') as $valor => $texto)
                    <option value="{{ $valor }}" @selected(old('estado', $producto->estado) === $valor)>{{ $texto }}</option>
                @endforeach
            </select>
            @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
        @endif
    </div>
    <div class="col-md-3 mb-3">
        <label for="fecha_alta" class="form-label">Fecha de alta</label>
        <input type="date" name="fecha_alta" id="fecha_alta"
               class="form-control @error('fecha_alta') is-invalid @enderror"
               value="{{ old('fecha_alta', $producto->fecha_alta?->format('Y-m-d')) }}">
        @error('fecha_alta') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-4">
    <label for="descripcion" class="form-label">Descripción / observaciones</label>
    <textarea name="descripcion" id="descripcion" rows="3"
              class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $producto->descripcion) }}</textarea>
    @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>