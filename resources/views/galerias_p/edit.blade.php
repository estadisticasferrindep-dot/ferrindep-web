@extends('layouts.app')

@section('title','Modificar foto')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <a style ="color:grey;" href="{{route('show_productos.index')}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al listado de productos</a>

            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('galeriasP.update', $galeria)}}" enctype="multipart/form-data" method="POST">
                            @csrf
                            @method('put')

                                <div class="form-group col-md-3">
                                    <label>Orden</label>
                                    <input type="text" name="orden" value="{{old('orden', $galeria->orden)}}" class="form-control" placeholder="Orden">
                                </div>

                                <div class="form-group">
                                    <label>Imagen</label>
                                    <input type="file" accept="image/*" name="imagen" value="{{old('imagen', $galeria->imagen)}}" class="form-control-file" >
                                </div>
                                
                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('show', $galeria->show) == 1 ? 'checked' : '' }} name="show" value="1">
                                <label class="form-check-label">Mostrar</label>
                            </div>
                            <br>

                            <button type="submit" class="btn btn-primary mb-2">Actualizar foto</button>

                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
