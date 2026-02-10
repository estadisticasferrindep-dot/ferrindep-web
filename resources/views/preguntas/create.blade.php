@extends('layouts.app')

@section('title','Pregunta nueva')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <a style ="color:grey;" href="{{route('preguntas.index')}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al listado de preguntas</a>

            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('preguntas.store')}}" method="POST">
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


                            <div class="form-group">
                                <label>Párrafo</label>
                                <textarea class="form-control summernote" name="parrafo"  rows="5">{{old('parrafo')}}</textarea>
                            </div>

                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('show') == 1 ? 'checked' : '' }} name="show" value="1">
                                <label class="form-check-label">Mostrar</label>
                            </div>
                            <br>

                            <button type="submit" class="btn btn-primary mb-2">Enviar Pregunta</button>

                        </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection