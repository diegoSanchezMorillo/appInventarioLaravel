@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 820px">
    <h1 class="h3 mb-3">Editar trabajador: {{ $trabajador->nombre_completo }}</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('trabajadores.update', $trabajador) }}" method="POST">
                @csrf
                @method('PUT')
                @include('trabajadores._form')
                <button class="btn btn-primary">Guardar cambios</button>
                <a href="{{ route('trabajadores.show', $trabajador) }}" class="btn btn-link">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection