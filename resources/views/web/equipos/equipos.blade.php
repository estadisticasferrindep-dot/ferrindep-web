@extends('layouts.plantilla')

@section('title','Equipos')

@section('content')

<!--INICIO SECCIÓN EQUIPOS-->        
<section class="section-nuestros-productos">
    <div class="container">

        <div class="row">
            
            <form method="GET" action="{{route('web.equipos.buscar')}}">
                <div class="row"> 
                        <div class="form-check col-6 col-md">
                            <input class="form-check-input" name="en_venta" type="checkbox" id="enVenta">
                            <label class="form-check-label" for="enVenta">
                            EN VENTA
                            </label>
                        </div>
        
                        <div class="form-check col-6 col-md">
                            <input class="form-check-input" name="en_alquiler" type="checkbox" id="enAlquiler">
                            <label class="form-check-label" for="enAlquiler">
                            EN ALQUILER
                            </label>
                        </div>
        
                        <div class="mb-3 col-12 col-md-2">
                            <select class="form-control" name="clase_id" id="claseEquipo">
                                <option value="" selected disabled>CLASE</option>
                                @foreach ($clases as $clase)
                                    <option {{ old('clase_id') == $clase->id ? 'selected' : '' }} value="{{$clase->id}}"> {{$clase->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
        
                        <div class="mb-3 col-12 col-md-3">
                            <select class="form-control" name="altura_id">
                                <option value="" selected disabled>ALTURA DE TRABAJO</option>
                                @foreach ($alturas as $altura)
                                    <option {{ old('altura_id') == $altura->id ? 'selected' : '' }} value="{{$altura->id}}"> {{$altura->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
        
                        <div class="mb-3 col-12 col-md-3">
                            <select class="form-control" name="combustion_id">
                                <option value="" selected disabled>TIPO DE COMBUSTIÓN</option>
                                @foreach ($combustiones as $combustion)
                                    <option {{ old('combustion_id') == $combustion->id ? 'selected' : '' }} value="{{$combustion->id}}"> {{$combustion->nombre}}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="mb-3 col btn-equipo-izquierda">BUSCAR</button>
                </div>
            </form>
        </div>

        <div class="row" style="margin-top: 22px; margin-bottom:48px;">
            @foreach ($clases as $clase)

                @if ($clase->show)

                    <div class="col-12 col-md-4" style="margin-bottom: 37px;">
                        <a href="{{ route('web.equipos.clase',$clase) }}" style="text-decoration: none">
                            <div class="box-clase">
                                <div class="img-border-equipos" style="background-image: url({{asset(Storage::url($clase->imagen))}}); "></div>
                                <p class="nombre-clase-equipo">{{$clase->nombre}}</p>
                            </div>
                        </a>
                    </div>
                @endif

            @endforeach
        </div>
    </div>
</section>

<!--FIN SECCIÓN EQUIPOS-->        

@endsection