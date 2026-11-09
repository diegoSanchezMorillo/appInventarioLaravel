@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 720px">
    <h1 class="h3 mb-3">Nueva categoría</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('categorias.store') }}" method="POST">
                @csrf
                @include('categorias._form')
                <button class="btn btn-primary">Guardar</button>
                <a href="{{ route('categorias.index') }}" class="btn btn-link">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection
