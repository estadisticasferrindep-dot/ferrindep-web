@extends('layouts.app')

@section('title','Modificar diámetro')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <a style ="color:grey;" href="{{route('productos.edit', $producto)}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al producto</a>

            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('diametrosP.update', [$diametro,$producto])}}" enctype="multipart/form-data" method="POST">
                            @csrf
                            @method('put')

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Orden</label>
                                    <input type="text" name="orden" value="{{old('orden',$diametro->orden)}}" class="form-control" placeholder="Orden">
                                </div>
                            </div>

                            <div class="form-group col-md-4">
                                <label>Tamaño</label>
                                <input  type="number" name="tamano" value="{{old('tamano',$diametro->tamano)}}" class="form-control">
                            </div>

                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('show',$diametro->show) == 1 ? 'checked' : '' }} name="show" value="1">
                                <label class="form-check-label">Mostrar</label>
                            </div>


                            <div class="form-group col-md-4">
                                <label>Precio</label>
                                <input step=".01" type="number" name="precio" value="{{old('precio',$diametro->precio)}}" class="form-control">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Precio anterior</label>
                                <input type="number" step=".01" name="precio_anterior" value="{{old('precio_anterior',$diametro->precio_anterior)}}" class="form-control">
                            </div>

                            <div class="form-group">
                                <label><h4 class="primer-h4">Tabla</h4></label>
                                <hr>
                                <textarea class="form-control summernote" name="tabla"  rows="4">{{old('tabla',$diametro->tabla)}}</textarea>
                            </div>


                            <button type="submit" class="btn btn-primary mb-2">Actualizar diámetro</button>

                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
