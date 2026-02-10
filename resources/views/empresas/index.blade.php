@extends('layouts.app')

@section('title','Empresa')

@section('content')
<div class="container cont-empresa">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    
                    <?php $empresa = $empresas->first() ?>


                    <form action="{{route('empresas.update',$empresa)}}" enctype="multipart/form-data" method="POST">
                                
                        @csrf
                        
                        @method('put')
                        

                        <div class="form-group">
                            <label><h4 class="primer-h4">Párrafo</h4></label>
                            <hr>
                            <textarea class="form-control summernote" name="parrafo"  rows="4">{{old('parrafo',$empresa->parrafo)}}</textarea>
                        </div>

                        <div class="form-group">
                            <label><h4 class="primer-h4">Imagen</h4></label>
                            <hr>
                            <div class="form-group">
                                <label>Imagen</label>
                                <input type="file" accept="image/*" name="imagen" value="{{old('imagen',$empresa->imagen)}}" class="form-control-file" >
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mb-2" >Actualizar Empresa</button>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection