<div class="mb-3">
    <label for="nombre" class="form-label">Nombre *</label>
    <input type="text" name="nombre" id="nombre"
           class="form-control @error('nombre') is-invalid @enderror"
           value="{{ old('nombre', $sede->nombre) }}" required>
    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label for="direccion" class="form-label">Dirección</label>
    <input type="text" name="direccion" id="direccion"
           class="form-control @error('direccion') is-invalid @enderror"
           value="{{ old('direccion', $sede->direccion) }}">
    @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="localidad" class="form-label">Localidad</label>
        <input type="text" name="localidad" id="localidad"
               class="form-control @error('localidad') is-invalid @enderror"
               value="{{ old('localidad', $sede->localidad) }}">
        @error('localidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="telefono" class="form-label">Teléfono</label>
        <input type="text" name="telefono" id="telefono"
               class="form-control @error('telefono') is-invalid @enderror"
               value="{{ old('telefono', $sede->telefono) }}">
        @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="form-check form-switch mb-4">
    <input type="checkbox" name="activa" id="activa" value="1" class="form-check-input"
           {{ old('activa', $sede->activa) ? 'checked' : '' }}>
    <label for="activa" class="form-check-label">Sede activa</label>
</div>