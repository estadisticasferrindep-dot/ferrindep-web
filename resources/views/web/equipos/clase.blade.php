@extends('layouts.plantilla')

@section('title','Equipos')

@section('content')


<section class="section-nuestros-productos">
    <div class="container">

        <div class="row">
            
            <form method="GET" action="{{route('web.equipos.buscar')}}">
                <div class="row"> 
                        <div class="form-check col-6 col-md">
                            <input class="form-check-input" name="en_venta" type="checkbox" id="enVenta" {{ $en_venta ? 'checked' : "" }}>
                            <label class="form-check-label" for="enVenta">
                            EN VENTA
                            </label>
                        </div>
        
                        <div class="form-check col-6 col-md">
                            <input class="form-check-input" name="en_alquiler" type="checkbox" id="enAlquiler" {{ $en_alquiler ? 'checked' : "" }}>
                            <label class="form-check-label" for="enAlquiler">
                            EN ALQUILER
                            </label>
                        </div>
        
                        <div class="mb-3 col-12 col-md-2">
                            <select class="form-control" name="clase_id" id="claseEquipo">
                                <option value="" selected disabled>CLASE</option>
                                @foreach ($clases as $itemClase)
                                    <option {{ old('clase_id') == $itemClase->id ? 'selected' : '' }} value="{{$itemClase->id}}"> {{$itemClase->nombre}}</option>
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
        <div class="row" style="margin-top: 22px; margin-bottom:64px;">
            @if ($clase)
            <div class="col-12 descripcion-clase">
                <h3>{{$clase->nombre}}</h3>
                <p>{{$clase->descripcion}}</p>
            </div>
            @endif


            @foreach ($equipos as $equipo)
                @if ($equipo->show)

                    @if ($en_venta && $en_alquiler)
                        @if ($equipo->en_venta && $equipo->en_alquiler)
                            <div class="col-12 col-md-4 item-producto" style="margin-bottom: 37px;">
                                <a href="{{ route('web.equipos.equipo',$equipo) }}" style="text-decoration: none">
                                    <div class="box-clase img-active">
                                        <div class="img-border-equipo" style="background-image: url({{asset(Storage::url($equipo->imagen_general))}}); "></div>
                                    </div>
                                </a>                           
                                <h3>{{$equipo->nombre}}</h3>
                                <p>{{$equipo->modelo}}</p>
                            </div>
                        @endif    
                    @else @if ($en_venta)
                        @if ($equipo->en_venta)
                            <div class="col-12 col-md-4 item-producto" style="margin-bottom: 37px;">
                                <a href="{{ route('web.equipos.equipo',$equipo) }}" style="text-decoration: none">
                                    <div class="box-clase img-active">
                                        <div class="img-border-equipo" style="background-image: url({{asset(Storage::url($equipo->imagen_general))}}); "></div>
                                    </div>
                                </a>                           
                                <h3>{{$equipo->nombre}}</h3>
                                <p>{{$equipo->modelo}}</p>
                            </div>
                        @endif  
                    @else @if ($en_alquiler)
                        @if ($equipo->en_alquiler)
                            <div class="col-12 col-md-4 item-producto" style="margin-bottom: 37px;">
                                <a href="{{ route('web.equipos.equipo',$equipo) }}" style="text-decoration: none">
                                    <div class="box-clase img-active">
                                        <div class="img-border-equipo" style="background-image: url({{asset(Storage::url($equipo->imagen_general))}}); "></div>
                                    </div>
                                </a>                           
                                <h3>{{$equipo->nombre}}</h3>
                                <p>{{$equipo->modelo}}</p>
                            </div>
                        @endif      
                    @else
                        <div class="col-12 col-md-4 item-producto" style="margin-bottom: 37px;">
                            <a href="{{ route('web.equipos.equipo',$equipo) }}" style="text-decoration: none">
                                <div class="box-clase img-active">
                                    <div class="img-border-equipo" style="background-image: url({{asset(Storage::url($equipo->imagen_general))}}); "></div>
                                </div>
                            </a>                           
                            <h3>{{$equipo->nombre}}</h3>
                            <p>{{$equipo->modelo}}</p>
                        </div>
                    @endif
                    @endif
                    @endif
                    
                @endif
            @endforeach
            
        </div>
    </div>
</section>

<!--FIN SECCIÓN EQUIPOS-->        

@endsection