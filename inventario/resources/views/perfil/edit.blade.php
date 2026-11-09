@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 640px">
    <h1 class="h3 mb-3">Mi perfil</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('perfil.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" value="{{ $usuario->email }}" disabled>
                    <div class="form-text">Para cambiar el email, pídeselo a un administrador.</div>
                </div>

                <div class="mb-4">
                    <label for="name" class="form-label">Nombre *</label>
                    <input type="text" name="name" id="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $usuario->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <h2 class="h6 text-muted text-uppercase">Cambiar contraseña</h2>
                <p class="small text-muted">Déjalo vacío si no quieres cambiarla.</p>

                <div class="mb-3">
                    <label for="password_actual" class="form-label">Contraseña actual</label>
                    <input type="password" name="password_actual" id="password_actual" autocomplete="current-password"
                           class="form-control @error('password_actual') is-invalid @enderror">
                    @error('password_actual') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Nueva contraseña</label>
                        <input type="password" name="password" id="password" autocomplete="new-password"
                               class="form-control @error('password') is-invalid @enderror">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="password_confirmation" class="form-label">Repite la nueva</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               autocomplete="new-password" class="form-control">
                    </div>
                </div>

                <button class="btn btn-primary">Guardar</button>
            </form>
        </div>
    </div>
</div>
@endsection
