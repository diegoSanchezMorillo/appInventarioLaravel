@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 900px">
    <h1 class="h3 mb-3">Nuevo producto</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('productos.store') }}" method="POST">
                @csrf
                @include('productos._form')
                <button class="btn btn-primary">Guardar</button>
                <a href="{{ route('productos.index') }}" class="btn btn-link">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection
