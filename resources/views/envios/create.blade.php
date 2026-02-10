@extends('layouts.app')

@section('title','Env{io} nuevo')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <a style ="color:grey;" href="{{route('envios.index')}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al listado de envios</a>

            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('envios.store')}}" enctype="multipart/form-data" method="POST">
                            @csrf

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Código Postal</label>
                                    <input type="number" name="cp" value="{{old('cp')}}" class="form-control" placeholder="Código Postal">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Costo</label>
                                    <input type="number" name="costo" value="{{old('costo')}}" class="form-control" placeholder="Costo">
                                </div>
                            </div>


                            <br>

                            <button type="submit" class="btn btn-primary mb-2">Enviar envío</button>

                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection