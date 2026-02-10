@extends('layouts.app')

@section('title','Añadir presentacion ')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <a style ="color:grey;" href="{{route('productos.edit',$producto)}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al producto</a>

            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('presentacionesP.store', $producto)}}" enctype="multipart/form-data" method="POST">
                            @csrf

                            <!-- <div class="form-group col-md-4">
                                <label>Orden</label>
                                <input type="text" name="orden" value="{{old('orden')}}" class="form-control" placeholder="Orden">
                            </div> -->

                            <div class="form-group col-md-4">
                                <label>Nombre </label>
                                <input type="text" name="nombre" value="{{old('nombre')}}" class="form-control" placeholder="Nombre">
                            </div>

                            <div class="form-group col-md-4">
                                <label>Cantidad límite de compra:</label>
                                <input  type="number" min="1" name="limite" value="{{old('limite')}}" class="form-control">
                            </div>

                            <div class="form-group col-md-4">
                                <label>Peso (Kg)</label>
                                <input step=".01" type="number" name="peso" value="{{old('peso')}}" class="form-control">
                            </div>

                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('free') == 1 ? 'checked' : '' }} name="free" value="1">
                                <label class="form-check-label">Envío gratis</label>
                            </div>

                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('show') == 1 ? 'checked' : '' }} name="show" value="1">
                                <label class="form-check-label">Mostrar</label>
                            </div>

                            <div class="form-group col-md-4">
                                <label>Precio </label>
                                <input step=".01" type="number" name="precio" value="{{old('precio')}}" class="form-control">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Precio anterior </label>
                                <input type="number" step=".01" name="precio_anterior" value="{{old('precio_anterior')}}" class="form-control">
                            </div>

                            <div class="form-group col-md-4">
                                <label>Stock disponible </label>
                                <input min="0" type="number" name="stock" value="{{old('stock')}}" class="form-control">
                            </div>

                            @if($producto->con_nombre)
                            <div class="form-group col-md-4">
                                <label>Medidas </label>
                                <input type="text" name="medidas" value="{{old('medidas')}}" class="form-control" >
                            </div>
                            @else

                            <div class="form-group col-md-4">
                                <label>Metros </label>
                                <input min="1" step=".01" type="number" name="metros" value="{{old('metros')}}" class="form-control">
                            </div>
                            
                            @endif

                            <br>

                            <button type="submit" class="btn btn-primary mb-2">Enviar presentacion</button>

                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
