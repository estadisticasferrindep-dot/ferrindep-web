@extends('layouts.app')

@section('title','Actualizar ancho')

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

                        <form action="{{route('categorias.update',$categoria)}}" enctype="multipart/form-data" method="POST">
                            @csrf
                            @method('put')

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Orden</label>
                                    <input type="text" name="orden" value="{{old('orden', $categoria->orden)}}" class="form-control" placeholder="Orden">
                                </div>
                                <div class="form-group col-md-8">
                                    <label>Ancho</label>
                                    <input type="text" name="nombre" value="{{old('nombre', $categoria->nombre)}}" class="form-control" placeholder="Ancho">
                                </div>
                                <div class="form-group">
                                    <label>Imagen</label>
                                    <input type="file" accept="image/*" name="imagen" value="{{old('imagen',$categoria->imagen)}}" class="form-control-file" >
                                </div>
                                <!-- <div class="form-group col-md-4">
                                    <label>Elige una familia para la categoría</label>
                                    <select class="form-control" name="familia_id">
                                        <option disabled>Elige una familia...</option>
                                            <option {{ old('familia_id', $categoria->familia_id) == 1 ? 'selected' : '' }} value="1"> Iluminación</option>
                                            <option {{ old('familia_id', $categoria->familia_id) == 2 ? 'selected' : '' }} value="2"> Ozono</option>
                                            <option {{ old('familia_id', $categoria->familia_id) == 3 ? 'selected' : '' }} value="3"> Repuestos</option>
                                            <option {{ old('familia_id', $categoria->familia_id) == 4 ? 'selected' : '' }} value="3"> Accesorios</option>

                                    </select>
                                </div> -->

                                
                            </div>
        

                        

                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('show', $categoria->show) == 1 ? 'checked' : '' }} name="show" value="1">
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