@extends('layouts.app')

@section('title','Servicio')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <a style ="color:grey;" href="{{route('servicios.index')}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al listado de servicios</a>

            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('servicios.update',$servicio)}}" enctype="multipart/form-data" method="POST">
                            
                            @csrf
                            @method('put')
                            
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Orden</label>
                                    <input type="text" name="orden" value="{{old('orden',$servicio->orden)}}" class="form-control" placeholder="Orden">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Título</label>
                                    <input type="text" name="titulo" value="{{old('titulo',$servicio->titulo)}}" class="form-control" placeholder="Título">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Imagen</label>
                                <input type="file" accept="image/*" name="imagen" value="{{old('imagen',$servicio->imagen)}}" class="form-control-file" >
                            </div>

                            <div class="form-group">
                                <label>Descrpción</label>
                                <textarea class="form-control" name="descripcion"  rows="3">{{old('descripcion',$servicio->descripcion)}}</textarea>
                            </div>

                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('show',$servicio->show) == 1 ? 'checked' : '' }} name="show" value="1">
                                <label class="form-check-label">Mostrar</label>
                            </div>

                            <br>

                            <button type="submit" class="btn btn-primary mb-2">Enviar servicio</button>

                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection