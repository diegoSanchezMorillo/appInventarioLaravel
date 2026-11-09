@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 820px">
    <h1 class="h3 mb-3">Editar usuario: {{ $usuario->name }}</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('usuarios.update', $usuario) }}" method="POST">
                @csrf
                @method('PUT')
                @include('usuarios._form')
                <button class="btn btn-primary">Guardar cambios</button>
                <a href="{{ route('usuarios.index') }}" class="btn btn-link">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection
