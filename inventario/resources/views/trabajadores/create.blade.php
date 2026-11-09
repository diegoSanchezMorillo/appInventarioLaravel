@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 820px">
    <h1 class="h3 mb-3">Nuevo trabajador</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('trabajadores.store') }}" method="POST">
                @csrf
                @include('trabajadores._form')
                <button class="btn btn-primary">Guardar</button>
                <a href="{{ route('trabajadores.index') }}" class="btn btn-link">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection