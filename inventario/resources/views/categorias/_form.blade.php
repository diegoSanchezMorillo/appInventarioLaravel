@php
    $prefijoBloqueado = $categoria->exists && $categoria->tieneProductos();
@endphp

<div class="mb-3">
    <label for="nombre" class="form-label">Nombre *</label>
    <input type="text" name="nombre" id="nombre"
           class="form-control @error('nombre') is-invalid @enderror"
           value="{{ old('nombre', $categoria->nombre) }}" required>
    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-4">
    <label for="prefijo" class="form-label">Prefijo de referencia *</label>
    <input type="text" name="prefijo" id="prefijo" maxlength="5"
           class="form-control font-monospace text-uppercase @error('prefijo') is-invalid @enderror"
           value="{{ old('prefijo', $categoria->prefijo) }}"
           {{ $prefijoBloqueado ? 'readonly' : 'required' }}>
    @error('prefijo') <div class="invalid-feedback">{{ $message }}</div> @enderror

    @if ($prefijoBloqueado)
        <div class="form-text text-warning">
            Esta categoría ya tiene productos, así que su prefijo no se puede cambiar.
        </div>
    @else
        <div class="form-text">
            Entre 2 y 5 letras. Los productos se numerarán así: <strong>PREFIJO-0001</strong>.
        </div>
    @endif
</div>