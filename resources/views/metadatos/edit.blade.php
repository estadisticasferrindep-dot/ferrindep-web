@extends('layouts.app')

@section('title','Metadato')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <a style ="color:grey;" href="{{route('metadatos.index')}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al listado de metadatos</a>

            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('metadatos.update',$metadato)}}" enctype="multipart/form-data" method="POST">
                            @csrf
                            @method('put')

                            <div class="form-group">
                                <label><h4 class="primer-h4">Keywords</h4></label>
                                <hr>
                                <textarea class="form-control" name="keywords"  rows="4">{{old('keywords',$metadato->keywords)}}</textarea>
                            </div>
                        
                            <div class="form-group">
                                <label><h4 class="primer-h4">Descripción</h4></label>
                                <hr>
                                <textarea class="form-control" name="descripcion"  rows="4">{{old('descripcion',$metadato->descripcion)}}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary mb-2">Actualizar metadato</button>



                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection