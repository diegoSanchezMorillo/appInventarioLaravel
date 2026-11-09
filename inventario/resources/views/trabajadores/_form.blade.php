<div class="row">
    <div class="col-md-5 mb-3">
        <label for="nombre" class="form-label">Nombre *</label>
        <input type="text" name="nombre" id="nombre"
               class="form-control @error('nombre') is-invalid @enderror"
               value="{{ old('nombre', $trabajador->nombre) }}" required>
        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-7 mb-3">
        <label for="apellidos" class="form-label">Apellidos *</label>
        <input type="text" name="apellidos" id="apellidos"
               class="form-control @error('apellidos') is-invalid @enderror"
               value="{{ old('apellidos', $trabajador->apellidos) }}" required>
        @error('apellidos') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="dni" class="form-label">DNI / NIE</label>
        <input type="text" name="dni" id="dni" maxlength="12"
               class="form-control font-monospace text-uppercase @error('dni') is-invalid @enderror"
               value="{{ old('dni', $trabajador->dni) }}">
        @error('dni') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-8 mb-3">
        <label for="sede_id" class="form-label">Sede habitual</label>
        <select name="sede_id" id="sede_id" class="form-select @error('sede_id') is-invalid @enderror">
            <option value="">— Sin sede asignada —</option>
            @foreach ($sedes as $sede)
                <option value="{{ $sede->id }}" @selected(old('sede_id', $trabajador->sede_id) == $sede->id)>
                    {{ $sede->nombre }}{{ $sede->activa ? '' : ' (inactiva)' }}
                </option>
            @endforeach
        </select>
        @error('sede_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-7 mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $trabajador->email) }}">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-5 mb-3">
        <label for="telefono" class="form-label">Teléfono</label>
        <input type="text" name="telefono" id="telefono"
               class="form-control @error('telefono') is-invalid @enderror"
               value="{{ old('telefono', $trabajador->telefono) }}">
        @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="form-check form-switch mb-4">
    <input type="checkbox" name="activo" id="activo" value="1" class="form-check-input"
           {{ old('activo', $trabajador->activo) ? 'checked' : '' }}>
    <label for="activo" class="form-check-label">Trabajador activo</label>
</div>
