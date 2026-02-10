@extends('layouts.app')

@section('title','Destino nuevo')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <a style ="color:grey;" href="{{route('destinos.index')}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al listado de anchos</a>

            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('destinos.store')}}" enctype="multipart/form-data" method="POST">
                            @csrf

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Nombre</label>
                                    <input type="text" name="nombre" value="{{old('nombre')}}" class="form-control" placeholder="Nombre">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Elige una zona para el producto</label>
                                    <select class="form-control" name="zona_id">
                                        <option disabled>Elige una zona...</option>
                                        @foreach ($zonas as $zona)
                                            <option {{ old('zona_id') == $zona->id ? 'selected' : '' }} value="{{$zona->id}}"> {{$zona->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mb-2">Enviar destino</button>
                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection