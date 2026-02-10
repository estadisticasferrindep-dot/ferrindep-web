@extends('layouts.app')


@section('title','Nueva imagen')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <a style ="color:grey;" href="{{route('imagenes.index')}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al listado de imagenes</a>

            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('imagenes.store')}}" enctype="multipart/form-data" method="POST">
                            @csrf

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>Orden</label>
                                    <input type="text" name="orden" value="{{old('orden')}}" class="form-control" placeholder="Orden">
                                </div>

                                <div class="form-group col-md-3">
                                    <label>Elige una ubicación para la imagen</label>
                                    <select class="form-control" name="ubicacion">
                                        <option disabled>Elige una ubicación...</option>
                                        <option {{ old('ubicacion') == 'Home' ? 'selected' : '' }} value="Home"> Home</option>
                                        <option {{ old('ubicacion') == 'Empresa' ? 'selected' : '' }} value="Empresa"> Empresa</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Imagen (Tamaño recomendado 1400x720)</label>
                                <input type="file" accept="image/*" name="imagen" value="{{old('imagen')}}" class="form-control-file" >
                            </div>

                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('show') == 1 ? 'checked' : '' }} name="show" value="1">
                                <label class="form-check-label">Mostrar</label>
                            </div>
                            <br>

                            <div class="form-group">
                                <label>Texto</label>
                                <textarea class="form-control summernote" name="texto"  rows="3">{{old('texto')}}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary mb-2">Enviar Imagen</button>

                        </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection