@extends('layouts.app')

@section('title','Zona nueva')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <a style ="color:grey;" href="{{route('zonas.index')}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al listado de anchos</a>

            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('zonas.store')}}" enctype="multipart/form-data" method="POST">
                            @csrf

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Nombre</label>
                                    <input type="text" name="nombre" value="{{old('nombre')}}" class="form-control" placeholder="Nombre">
                                </div>


                            <br>

                            <button type="submit" class="btn btn-primary mb-2">Enviar zona</button>

                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection