@extends('layouts.app')

@section('title','Actualizar producto')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <a style ="color:grey;" href="{{route('productos.index')}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al listado de productos</a>

            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('productos.update',$producto)}}" enctype="multipart/form-data" method="POST">
                            @csrf
                            @method('put')
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Orden</label>
                                    <input type="text" name="orden" value="{{old('orden',$producto->orden)}}" class="form-control" placeholder="Orden">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Nombre</label>
                                    <input type="text" name="nombre" value="{{old('nombre',$producto->nombre)}}" class="form-control" placeholder="Nombre">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Modelo</label>
                                    <input type="text" name="modelo" value="{{old('modelo',$producto->modelo)}}" class="form-control" placeholder="Modelo">
                                </div>
                            </div>

                            <div class="form-group">
                                <label><h4 class="primer-h4">Descripción</h4></label>
                                <hr>
                                <textarea class="form-control" name="descripcion"  rows="4">{{old('descripcion',$producto->descripcion)}}</textarea>
                            </div>

                            <div class="form-group">
                                <label>Imagen</label>
                                <input type="file" accept="image/*" name="imagen" value="{{old('imagen',$producto->imagen)}}" class="form-control-file" >
                            </div>

                            
                            <div class="form-group">
                                <label>Ficha técnica</label>
                                <input type="file" name="ficha_tecnica" value="{{old('ficha_tecnica',$producto->ficha_tecnica)}}" class="form-control-file" >
                            </div>


                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('show',$producto->show) == 1 ? 'checked' : '' }} name="show" value="1">
                                <label class="form-check-label">Mostrar</label>
                            </div>

                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('oferta',$producto->oferta) == 1 ? 'checked' : '' }} name="oferta" value="1">
                                <label class="form-check-label">En oferta</label>
                            </div>
                            <br>

                            <button type="submit" class="btn btn-primary mb-2">Enviar producto</button>

                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
