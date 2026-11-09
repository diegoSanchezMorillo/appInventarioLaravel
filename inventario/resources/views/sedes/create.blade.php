@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 720px">
    <h1 class="h3 mb-3">Nueva sede</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('sedes.store') }}" method="POST">
                @csrf
                @include('sedes._form')
                <button class="btn btn-primary">Guardar</button>
                <a href="{{ route('sedes.index') }}" class="btn btn-link">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection
