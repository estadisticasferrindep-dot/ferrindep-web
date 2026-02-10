@extends('layouts.app')

@section('title','Ancho nuevo')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <a style ="color:grey;" href="{{route('categorias.index')}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al listado de anchos</a>

            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('categorias.store')}}" enctype="multipart/form-data" method="POST">
                            @csrf

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Orden</label>
                                    <input type="text" name="orden" value="{{old('orden')}}" class="form-control" placeholder="Orden">
                                </div>
                                <div class="form-check col-md-4">
                                <input class="form-check-input" type="checkbox" {{ old('con_nombre') == 1 ? 'checked' : '' }} name="con_nombre" value="1">
                                <label class="form-check-label">Con nombre</label>
                            </div>
                                <div class="form-group col-md-4">
                                    <label>Ancho/Nombre</label>
                                    <input type="text" name="nombre" value="{{old('nombre')}}" class="form-control" placeholder="Ancho">
                                </div>

                                <div class="form-group">
                                    <label>Imagen</label>
                                    <input type="file" accept="image/*" name="imagen" value="{{old('imagen')}}" class="form-control-file" >
                                </div>
<!-- 
                                <div class="form-group col-md-4">
                                    <label>Elige una familia para la categoría</label>
                                    <select class="form-control" name="familia_id">
                                        <option disabled>Elige una familia...</option>
                                            <option {{ old('familia_id') == 1 ? 'selected' : '' }} value="1"> Iluminación</option>
                                            <option {{ old('familia_id') == 2 ? 'selected' : '' }} value="2"> Ozono</option>
                                            <option {{ old('familia_id') == 3 ? 'selected' : '' }} value="3"> Repuestos</option>
                                            <option {{ old('familia_id') == 4 ? 'selected' : '' }} value="4"> Accesorios</option>

                                    </select>
                                </div> -->
                                 
                            </div>
        

                        

                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('show') == 1 ? 'checked' : '' }} name="show" value="1">
                                <label class="form-check-label">Mostrar</label>
                            </div>
                            

                            <br>

                            <button type="submit" class="btn btn-primary mb-2">Enviar ancho</button>

                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection