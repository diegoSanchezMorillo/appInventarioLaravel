@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 720px">
    <h1 class="h3 mb-3">Editar sede: {{ $sede->nombre }}</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('sedes.update', $sede) }}" method="POST">
                @csrf
                @method('PUT')
                @include('sedes._form')
                <button class="btn btn-primary">Guardar cambios</button>
                <a href="{{ route('sedes.index') }}" class="btn btn-link">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection
