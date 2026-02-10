@extends('layouts.app')

@section('title','Familia nueva')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <a style ="color:grey;" href="{{route('familias.index')}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al listado de familias</a>

            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('familias.store')}}" enctype="multipart/form-data" method="POST">
                            @csrf

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Orden</label>
                                    <input type="text" name="orden" value="{{old('orden')}}" class="form-control" placeholder="Orden">
                                </div>
                                <div class="form-group col-md-8">
                                    <label>Familia</label>
                                    <input type="text" name="nombre" value="{{old('nombre')}}" class="form-control" placeholder="Familia">
                                </div>

                                {{-- <div class="form-group">
                                    <label>Imagen</label>
                                    <input type="file" accept="image/*" name="imagen" value="{{old('imagen')}}" class="form-control-file" >
                                </div> --}}
                            </div>
        

                        

                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('show') == 1 ? 'checked' : '' }} name="show" value="1">
                                <label class="form-check-label">Mostrar</label>
                            </div>


                            <br>

                            <button type="submit" class="btn btn-primary mb-2">Enviar familia</button>

                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection