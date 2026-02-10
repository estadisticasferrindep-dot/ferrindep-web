@extends('layouts.app')


@section('title','Nueva foto')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <a style ="color:grey;" href="{{route('fotos.index')}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al listado de fotos</a>

            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('fotos.store')}}" enctype="multipart/form-data" method="POST">
                            @csrf

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Orden</label>
                                    <input type="text" name="orden" value="{{old('orden')}}" class="form-control" placeholder="Orden">
                                </div>

                                <div class="form-group col-md-8">
                                    <label>Descripción</label>
                                    <input type="text" name="descripcion" value="{{old('descripcion')}}" class="form-control" placeholder="Descripción">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Imagen</label>
                                <input type="file" accept="image/*" name="imagen" value="{{old('imagen')}}" class="form-control-file" >
                            </div>

                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('show') == 1 ? 'checked' : '' }} name="show" value="1">
                                <label class="form-check-label">Mostrar</label>
                            </div>
                            <br>

                            <button type="submit" class="btn btn-primary mb-2">Enviar foto</button>

                        </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection