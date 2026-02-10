@extends('layouts.app')

@section('title','Precio de envío nuevo')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <a style ="color:grey;" href="{{route('pesozonas.index')}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al listado de anchos</a>

            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('pesozonas.store')}}" enctype="multipart/form-data" method="POST">
                            @csrf

                            <div class="form-row">

                                <div class="form-group col-md-4">
                                    <label>Peso</label>
                                    <input step=".01" type="number" name="peso" value="{{old('peso')}}" class="form-control">
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

                                <div class="form-group col-md-4">
                                    <label>Precio</label>
                                    <input step=".01" type="number" name="costo" value="{{old('costo')}}" class="form-control">
                                </div> 
                            </div>
                            <br>

                            <button type="submit" class="btn btn-primary mb-2">Enviar precio de envío</button>

                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection