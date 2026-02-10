@extends('layouts.app')

@section('title','Video nuevo')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <a style ="color:grey;" href="{{route('videos.index')}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al listado de videos</a>

            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('videos.store')}}" enctype="multipart/form-data" method="POST">
                            @csrf

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Orden</label>
                                    <input type="text" name="orden" value="{{old('orden')}}" class="form-control" placeholder="Orden">
                                </div>
                                <div class="form-group col-md-8">
                                    <label>Título</label>
                                    <input type="text" name="titulo" value="{{old('titulo')}}" class="form-control" placeholder="Título">
                                </div>
                            </div>

                            <div class="form-group col-md-8">
                                    <label>Subtítulo</label>
                                    <input type="text" name="subtitulo" value="{{old('subtitulo')}}" class="form-control" placeholder="Subtítulo">
                            </div>


                            <div class="form-group">
                                <label>Link del Video (solo el código)</label>
                                <input type="text" name="link" value="{{old('link')}}" class="form-control-file" >
                            </div>

                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('show') == 1 ? 'checked' : '' }} name="show" value="1">
                                <label class="form-check-label">Mostrar</label>
                            </div>
                            <br>

                            <button type="submite" class="btn btn-primary mb-2">Enviar video</button>

                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection