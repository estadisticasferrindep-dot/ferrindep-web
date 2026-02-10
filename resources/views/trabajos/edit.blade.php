@extends('layouts.app')

@section('title','Actualizar trabajo')

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

                        <form action="{{route('trabajos.update',$trabajo)}}" enctype="multipart/form-data" method="POST">
                            @csrf
                            @method('put')
        
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Orden</label>
                                    <input type="text" name="orden" value="{{old('orden', $trabajo->orden)}}" class="form-control" placeholder="Orden">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Nombre</label>
                                    <input type="text" name="nombre" value="{{old('nombre', $trabajo->nombre)}}" class="form-control" placeholder="Nombre con siglas">
                                </div>
                            </div>

                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('home', $trabajo->home) == 1 ? 'checked' : '' }} name="home" value="1">
                                <label class="form-check-label">Mostrar en Home</label>
                            </div>


                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('show', $trabajo->show) == 1 ? 'checked' : '' }} name="show" value="1">
                                <label class="form-check-label">Mostrar</label>
                            </div>
                            
                            <div class="form-group col-md-4">
                                <label>Elige tres poductos relacionados</label>
                                <select class="form-control" name="relacionado_1">
                                    <option disabled>Elige un trabajo...</option>
                                    @foreach ($relacionados as $relacionado)
                                        <option {{ old('relacionado_1', $trabajo->relacionado_1) == $relacionado->id ? 'selected' : '' }} value="{{$relacionado->id}}"> {{$relacionado->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <select class="form-control" name="relacionado_2">
                                    <option disabled>Elige una trabajo...</option>
                                    @foreach ($relacionados as $relacionado)
                                        <option {{ old('relacionado_2', $trabajo->relacionado_2) == $relacionado->id ? 'selected' : '' }} value="{{$relacionado->id}}"> {{$relacionado->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <select class="form-control" name="relacionado_3">
                                    <option disabled>Elige una trabajo...</option>
                                    @foreach ($relacionados as $relacionado)
                                        <option {{ old('relacionado_3', $trabajo->relacionado_3) == $relacionado->id ? 'selected' : '' }} value="{{$relacionado->id}}"> {{$relacionado->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Imagen </label>
                                <input type="file" accept="image/*" name="imagen" value="{{old('imagen', $trabajo->imagen)}}" class="form-control-file" >
                            </div>

                            <div class="form-group ">
                                <label>Tabla</label>
                                <textarea class="form-control summernote" name="tabla" >{{old('tabla', $trabajo->tabla)}}</textarea>
                            </div>


                            <button type="submit" class="btn btn-primary mb-2">Enviar trabajo</button>
                        </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection