@extends('layouts.app')

@section('title', 'Editar Mapeo de Ubicación')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-11">
                <a style="color:grey;" href="{{route('configuracion_ubicacion.index')}}"><i class="fas fa-arrow-circle-left"
                        style="color:grey; margin-right:6px;"></i>Volver al listado</a>

                <div class="card" style="margin-top:15px;">

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{route('configuracion_ubicacion.update', $mapeo->id)}}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Ciudad Detectada (Exacta)</label>
                                    <input type="text" name="ciudad_detectada"
                                        value="{{old('ciudad_detectada', $mapeo->ciudad_detectada)}}" class="form-control"
                                        placeholder="Ej: Villa Ballester">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Asignar a Destino</label>
                                    <select class="form-control" name="destino_id">
                                        <option value="" disabled>Elige un destino...</option>
                                        @foreach ($destinos as $destino)
                                            <option {{ old('destino_id', $mapeo->destino_id) == $destino->id ? 'selected' : '' }}
                                                value="{{$destino->id}}"> {{$destino->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary mt-3">Actualizar Mapeo</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection