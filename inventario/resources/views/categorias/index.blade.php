@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Categorías</h1>
        <a href="{{ route('categorias.create') }}" class="btn btn-primary">Nueva categoría</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Prefijo</th>
                        <th class="text-center">Productos</th>
                        <th>Próxima referencia</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categorias as $categoria)
                        <tr>
                            <td class="fw-semibold">{{ $categoria->nombre }}</td>
                            <td><span class="badge bg-dark font-monospace">{{ $categoria->prefijo }}</span></td>
                            <td class="text-center">{{ $categoria->productos_count }}</td>
                            <td class="font-monospace text-muted">{{ $categoria->siguiente_referencia }}</td>
                            <td class="text-end">
                                <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar la categoría {{ $categoria->nombre }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No hay categorías registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $categorias->links() }}
    </div>
</div>
@endsection