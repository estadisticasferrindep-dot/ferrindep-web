@extends('layouts.app')

@section('title','Sección Nueva')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <a style ="color:grey;" href="{{route('show_trabajos.index')}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al listado de trabajos</a>

            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('secciones.store', $trabajo)}}" enctype="multipart/form-data" method="POST">
                            @csrf

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>Orden</label>
                                    <input type="text" name="orden" value="{{old('orden')}}" class="form-control" placeholder="Orden">
                                </div>

                                <div class="form-group col-md-3">
                                    <label>Título</label>
                                    <input type="text" name="titulo" value="{{old('titulo')}}" class="form-control" placeholder="Título">
                                </div>
                            </div>

                            <div class="form-group">
                                <label><h4 class="primer-h4">Párrafo</h4></label>
                                <hr>
                                <textarea class="form-control summernote" name="texto"  rows="4">{{old('texto')}}</textarea>
                            </div>

                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('show') == 1 ? 'checked' : '' }} name="show" value="1">
                                <label class="form-check-label">Mostrar</label>
                            </div>
                            <br>

                            <button type="submit" class="btn btn-primary mb-2">Enviar sección</button>

                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
