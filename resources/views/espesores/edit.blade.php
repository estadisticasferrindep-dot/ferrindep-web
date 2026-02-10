@extends('layouts.app')

@section('title','Actualizar espesor')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <a style ="color:grey;" href="{{route('espesores.index')}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al listado de espesores</a>

            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('espesores.update',$espesor)}}" enctype="multipart/form-data" method="POST">
                            @csrf
                            @method('put')

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Espesor</label>
                                    <input type="text" name="espesor" value="{{old('espesor', $espesor->espesor)}}" class="form-control" placeholder="Espesor">
                                </div>
                            </div>

                            <div class="form-group col-md-4">
                                    <label>Orden</label>
                                    <input type="text" name="orden" value="{{old('orden',$espesor->orden)}}" class="form-control" placeholder="Orden">
                                </div>

                            <br>

                            <br>

                            <button type="submit" class="btn btn-primary mb-2">Enviar espesor</button>

                        </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection