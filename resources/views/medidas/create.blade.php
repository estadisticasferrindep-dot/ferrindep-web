@extends('layouts.app')

@section('title','Medida nueva')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <a style ="color:grey;" href="{{route('medidas.index')}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al listado de medidas</a>

            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('medidas.store')}}" enctype="multipart/form-data" method="POST">
                            @csrf

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Medidas</label>
                                    <input type="text" name="medidas" value="{{old('medidas')}}" class="form-control" placeholder="Medidas">
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                    <label>Orden</label>
                                    <input type="text" name="orden" value="{{old('orden')}}" class="form-control" placeholder="Orden">
                                </div>

                            <br>

                            <button type="submit" class="btn btn-primary mb-2">Enviar medida</button>

                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection