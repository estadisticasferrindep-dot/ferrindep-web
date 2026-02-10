@extends('layouts.app')

@section('title','Noticia')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <a style ="color:grey;" href="{{route('noticias.index')}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al listado de noticias</a>

            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('noticias.update',$noticia)}}" enctype="multipart/form-data" method="POST">
                            
                            @csrf
                            @method('put')
                            
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Orden</label>
                                    <input type="text" name="orden" value="{{old('orden',$noticia->orden)}}" class="form-control" placeholder="Orden">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Título</label>
                                    <input type="text" name="titulo" value="{{old('titulo',$noticia->titulo)}}" class="form-control" placeholder="Título">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Elige una categoría para la noticia</label>
                                    <select class="form-control" name="claseblog_id">
                                        <option disabled>Elige una categoría...</option>
                                        @foreach ($claseblogs as $claseblog)
                                            <option {{ old('claseblog_id',$noticia->claseblog_id) == $claseblog->id ? 'selected' : '' }} value="{{$claseblog->id}}"> {{$claseblog->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Imagen</label>
                                <input type="file" accept="image/*" name="imagen" value="{{old('imagen',$noticia->imagen)}}" class="form-control-file" >
                            </div>

                            <div class="form-group">
                                <label>Descrpción</label>
                                <textarea class="form-control" name="descripcion"  rows="3">{{old('descripcion',$noticia->descripcion)}}</textarea>
                            </div>

                            <div class="form-group">
                                <label>Características</label>
                                <textarea class="form-control summernote" name="caracteristicas"  rows="3">{{old('caracteristicas',$noticia->caracteristicas)}}</textarea>
                            </div>

                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('show',$noticia->show) == 1 ? 'checked' : '' }} name="show" value="1">
                                <label class="form-check-label">Mostrar</label>
                            </div>

                            
                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('destacar',$noticia->destacar) == 1 ? 'checked' : '' }} name="destacar" value="1">
                                <label class="form-check-label">Destacar</label>
                            </div>

                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('home',$noticia->home) == 1 ? 'checked' : '' }} name="home" value="1">
                                <label class="form-check-label">Mostrar en Home</label>
                            </div>
                            <br>

                            <button type="submit" class="btn btn-primary mb-2">Enviar noticia</button>

                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection