@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 900px">
    <h1 class="h3 mb-3">
        Editar <span class="font-monospace">{{ $producto->referencia }}</span>
    </h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('productos.update', $producto) }}" method="POST">
                @csrf
                @method('PUT')
                @include('productos._form')
                <button class="btn btn-primary">Guardar cambios</button>
                <a href="{{ route('productos.show', $producto) }}" class="btn btn-link">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection
