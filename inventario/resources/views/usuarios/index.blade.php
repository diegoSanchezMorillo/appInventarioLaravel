@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Usuarios</h1>
        <a href="{{ route('usuarios.create') }}" class="btn btn-primary">Nuevo usuario</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th class="text-end"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usuarios as $usuario)
                        <tr @class(['text-muted' => ! $usuario->activo])>
                            <td class="fw-semibold">
                                {{ $usuario->name }}
                                @if ($usuario->is(auth()->user()))
                                    <span class="badge bg-light text-dark border">Tú</span>
                                @endif
                            </td>
                            <td>{{ $usuario->email }}</td>
                            <td>
                                @if ($usuario->es_admin)
                                    <span class="badge bg-dark">Administrador</span>
                                @else
                                    <span class="badge bg-light text-dark border">Usuario</span>
                                @endif
                            </td>
                            <td>
                                @if ($usuario->activo)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Desactivado</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $usuarios->links() }}
    </div>
</div>
@endsection
