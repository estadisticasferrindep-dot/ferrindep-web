@extends('layouts.app')

@section('title','Añadir rango ')

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

                        <form action="{{route('rangosP.store', $producto)}}" enctype="multipart/form-data" method="POST">
                            @csrf




                            <div class="form-group col-md-4">
                                <label>Hasta esta cantidad:</label>
                                <input  type="number" name="max" value="{{old('max')}}" class="form-control">
                            </div>

                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('ultimo') == 1 ? 'checked' : '' }} name="ultimo" value="1">
                                <label class="form-check-label">Es el último rango?</label>
                            </div>

                            <div class="form-group col-md-4">
                                <label>Precio </label>
                                <input step=".01" type="number" name="precio" value="{{old('precio')}}" class="form-control">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Precio anterior </label>
                                <input type="number" step=".01" name="precio_anterior" value="{{old('precio_anterior')}}" class="form-control">
                            </div>


                            
                            <br>

                            <button type="submit" class="btn btn-primary mb-2">Enviar rango</button>

                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
