@php
    $soyYo = $usuario->exists && $usuario->is(auth()->user());
@endphp

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="name" class="form-label">Nombre *</label>
        <input type="text" name="name" id="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $usuario->name) }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="email" class="form-label">Email *</label>
        <input type="email" name="email" id="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $usuario->email) }}" required>
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="password" class="form-label">Contraseña {{ $usuario->exists ? '' : '*' }}</label>
        <input type="password" name="password" id="password" autocomplete="new-password"
               class="form-control @error('password') is-invalid @enderror"
               {{ $usuario->exists ? '' : 'required' }}>
        @if ($usuario->exists)
            <div class="form-text">Déjala vacía para no cambiarla.</div>
        @else
            <div class="form-text">Mínimo 8 caracteres.</div>
        @endif
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="password_confirmation" class="form-label">Repite la contraseña</label>
        <input type="password" name="password_confirmation" id="password_confirmation"
               autocomplete="new-password" class="form-control">
    </div>
</div>

<div class="mb-2 form-check form-switch">
    <input type="checkbox" name="es_admin" id="es_admin" value="1" class="form-check-input"
           {{ old('es_admin', $usuario->es_admin) ? 'checked' : '' }} @disabled($soyYo)>
    <label for="es_admin" class="form-check-label">Administrador (puede gestionar usuarios)</label>
</div>

<div class="mb-4 form-check form-switch">
    <input type="checkbox" name="activo" id="activo" value="1" class="form-check-input"
           {{ old('activo', $usuario->activo) ? 'checked' : '' }} @disabled($soyYo)>
    <label for="activo" class="form-check-label">Cuenta activa (puede iniciar sesión)</label>
</div>

@if ($soyYo)
    <div class="alert alert-light border small">No puedes quitarte el rol de administrador ni desactivar tu propia cuenta.</div>
@endif