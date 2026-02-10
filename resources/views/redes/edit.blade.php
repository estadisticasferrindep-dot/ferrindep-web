@extends('layouts.app')

@section('title','Editar red social')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <a style ="color:grey;" href="{{route('redes.index')}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al listado de redes</a>

            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('redes.update',$red)}}" enctype="multipart/form-data" method="POST">
                            @csrf
                            @method('put')

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Orden</label>
                                    <input type="text" name="orden" value="{{old('orden',$red->orden)}}" class="form-control" placeholder="Orden">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Elige una red social</label>
                                    <select class="form-control" name="icono">
                                        <option disabled>Elige una red social...</option>
                                        <option {{ old('icono') == '<i class="fab fa-instagram"></i>' ? 'selected' : '' }} value='<i class="fab fa-instagram"></i>'>Instagram</option>
                                        <option {{ old('icono') == '<i class="fab fa-twitter"></i>' ? 'selected' : '' }} value='<i class="fab fa-twitter"></i>'>Twitter</option>
                                        <option {{ old('icono') == '<i class="fab fa-facebook-f"></i>' ? 'selected' : '' }} value='<i class="fab fa-facebook-f"></i>'>Facebook</option>
                                        <option {{ old('icono') == '<i class="fab fa-youtube"></i>' ? 'selected' : '' }} value='<i class="fab fa-youtube"></i>'>YouTube</option>
                                        <option {{ old('icono') == '<i class="fab fa-linkedin-in"></i>' ? 'selected' : '' }} value='<i class="fab fa-linkedin-in"></i>'>LinkedIn</option>
                                        <option {{ old('icono') == '<i class="fab fa-telegram-plane"></i>' ? 'selected' : '' }} value='<i class="fab fa-telegram-plane"></i>'>Telegram</option>

                                    </select>

                                    
                                </div>
                            
                                <div class="form-group col-md-4">
                                    <label>URL</label>
                                    <input type="text" name="url" value="{{old('url')}}" class="form-control" placeholder="URL">
                                </div>
                            </div>

                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" {{ old('show',$red->show) == 1 ? 'checked' : '' }} name="show" value="1">
                                <label class="form-check-label">Mostrar</label>
                            </div>
                            <br>

                            <button type="submit" class="btn btn-primary mb-2">Actualizar red social</button>



                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection