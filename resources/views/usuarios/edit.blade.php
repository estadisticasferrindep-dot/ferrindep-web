@extends('layouts.app')

@section('title','Cliente')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <a style ="color:grey;" href="{{route('servicios.index')}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al listado de clientes</a>

            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('usuarios.update',$usuario)}}" enctype="multipart/form-data" method="POST">
                            
                            @csrf
                            @method('put')
                            
                            <div class="form-group col-md-4">
                                <label>Tipo de usuario</label>
                                <select class="form-control" name="tipo_cliente">
                                    <option disabled>Elige una tipo de usuario...</option>
                                        <option {{ old('tipo_cliente', $usuario->tipo_cliente) == "publico" ? 'selected' : '' }} value="publico"> Público</option>
                                        <option {{ old('tipo_cliente', $usuario->tipo_cliente) == "mayorista" ? 'selected' : '' }} value="mayorista"> Mayorista</option>
                                        <option {{ old('tipo_cliente', $usuario->tipo_cliente) == "gremio" ? 'selected' : '' }} value="gremio"> Gremio</option>
                                        <option {{ old('tipo_cliente', $usuario->tipo_cliente) == "especial" ? 'selected' : '' }} value="especial"> Especial</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary mb-2">Enviar tipo de cliente</button>
                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection