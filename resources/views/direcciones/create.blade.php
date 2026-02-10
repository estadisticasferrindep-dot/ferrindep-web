@extends('layouts.app')

@section('title','Dirección nueva')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <a style ="color:grey;" href="{{route('direcciones.index')}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al listado de categorías de noticias</a>

            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('direcciones.store')}}" method="POST">
                            @csrf

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Orden</label>
                                    <input type="text" name="orden" value="{{old('orden')}}" class="form-control" placeholder="Orden">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Nombre</label>
                                    <input type="text" name="nombre" value="{{old('nombre')}}" class="form-control" placeholder="Nombre">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Email</label>
                                    <input type="email" name="email" value="{{old('email')}}" class="form-control" placeholder="Email">
                                </div>
                                <div class="form-group col-md-8">
                                    <label>Dirección</label>
                                    <input type="text" name="direccion" value="{{old('direccion')}}" class="form-control" placeholder="Dirección">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Teléfonos</label>
                                <textarea class="form-control summernote" name="telefonos"  rows="3">{{old('telefonos')}}</textarea>
                            </div>

                            <div class="form-group">
                                <label>Iframe del mapa</label>
                                <textarea class="form-control" name="map"  rows="3">{{old('map')}}</textarea>
                            </div>

                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('show') == 1 ? 'checked' : '' }} name="show" value="1">
                                <label class="form-check-label">Mostrar</label>
                            </div>

                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('footer') == 1 ? 'checked' : '' }} name="footer" value="1">
                                <label class="form-check-label">Mostrar en el footer (no más de 2)</label>
                            </div>
                            <br>

                            <button type="submit" class="btn btn-primary mb-2">Enviar dirección</button>

                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection