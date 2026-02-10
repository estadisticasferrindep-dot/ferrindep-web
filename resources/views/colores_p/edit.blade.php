@extends('layouts.app')

@section('title','Modificar color')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <a style ="color:grey;" href="{{route('productos.edit', $producto)}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al producto</a>

            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('coloresP.update', [$cp,$producto])}}" enctype="multipart/form-data" method="POST">
                            @csrf
                            @method('put')

                            <div class="form-group col-md-4">
                                <label>Elige una color para el producto</label>
                                <select class="form-control" name="color_id">
                                    <option disabled>Elige una color...</option>
                                    @foreach ($colores as $color)
                                        <option {{ old('color_id',$cp->color_id) == $color->id ? 'selected' : '' }} value="{{$color->id}}"> {{$color->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>Orden</label>
                                    <input type="text" name="orden" value="{{old('orden',$cp->orden)}}" class="form-control" placeholder="Orden">
                                </div>
                            </div>
                            
                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('show',$cp->show) == 1 ? 'checked' : '' }} name="show" value="1">
                                <label class="form-check-label">Mostrar</label>
                            </div>
                            <br>

                            <button type="submit" class="btn btn-primary mb-2">Actualizar color</button>

                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
