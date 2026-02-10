@extends('layouts.plantilla')

@section('title', strtoupper($equipo->nombre))

@section('content')


<!--INICIO EQUIPO-->

            <section class="section-equipo">
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="box-clase">
                                <div class="img-border-equipos" style="background-image: url({{asset(Storage::url($equipo->imagen_detalle))}}); "></div>
                            </div>

                            <div style="display: flex">
                                <div class="box-clase-mini">
                                    <img class=" img img-1" src="{{asset(Storage::url($equipo->imagen_general))}}">
                                </div>
                                <div class="box-clase-mini">
                                    <img class=" img img-2" src="{{asset(Storage::url($equipo->imagen_general))}}">
                                </div>
                            </div>

                        </div>
                        <div class="col-12 col-md-6">
                            <h3>{{$equipo->nombre}}</h3>
                            <h4>{{$equipo->modelo}}</h4>
                            {{-- <ul class="list-group list-group-flush">
                                <li class="list-group-item"><p> Capacidad</p><span>{{$equipo->capacidad}}</span></li>
                                <li class="list-group-item"><p>Medidas</p><span>{{$equipo->medidas}}</span></li>
                                <li class="list-group-item"><p>Altura de Trabajo</p><span>{{$equipo->altura_de_trabajo}}</span></li>
                                <li class="list-group-item"><p>Altura de Plataforma</p><span>{{$equipo->altura_de_plataforma}}</span></li>
                                <li class="list-group-item"><p>Tamaño Global</p><span>{{$equipo->tamaño_global}}</span></li>
                                <li class="list-group-item"><p>Min. distancia piso</p><span>{{$equipo->min_distancia_piso}}</span></li>
                                <li class="list-group-item"><p>Distancia entre ejes</p><span>{{$equipo->distancia_entre_ejes}}</span></li>
                                <li class="list-group-item"><p>Velocidad Ascenso y descenso</p><span>{{$equipo->velocidad_ascenso_descenso}}</span></li>
                                <li class="list-group-item"><p>Motor electrico</p><span>{{$equipo->motor_electrico}}</span></li>
                                <li class="list-group-item"><p>Bateria</p><span>{{$equipo->bateria}}</span></li>
                                <li class="list-group-item"><p>Tamaño ruedas</p><span>{{$equipo->tamaño_ruedas}}</span></li>
                                <li class="list-group-item"><p>Peso neto</p><span>{{$equipo->peso_neto}}</span></li>
                            </ul> --}}
                            <div class="tabla-producto">
                                {!!$equipo->tabla!!}
                            </div>


                            <div style="display: flex; justify-content:space-between;margin-top:39px">
                                <a  href="{{asset(Storage::url($equipo->ficha_tecnica))}}"  download>
                                <button type="submit" class="btn-equipo-izquierda"  >
                                    DESCARGAR FICHA TECNICA 
                                </button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section-equipo-contacto">
                <div class="container">
                    <h2>Contactá con un experto</h2>
                    <form method="POST" action="{{route('web.contactanos_experto_equipo',$equipo)}}"> 
                        @csrf
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <input class="box-equipo" name="nombre" placeholder="Ingresar nombre *">
                                <input class="box-equipo" name="email" placeholder="Ingrese su correo electrónico *">
                                <input class="box-equipo" name="empresa" placeholder="Empresa *">

                            </div>

                            <div class="col-12 col-md-4">
                                <input class="box-equipo" name="telefono" placeholder="Ingrese número de teléfono*">
                                <div> 
                                    <textarea class="box-equipo" name="mensaje" style="padding-top:19px; min-height:128px;">Mensaje</textarea>
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <h4> Tipo de operación </h4>
                                    <div class="form-check form-check-inline" style="margin-bottom: 5px;">
                                        <input class="form-check-input" type="checkbox" name="en_venta" value="1">
                                        <label class="form-check-label">Venta</label>
                                    </div>
                                    <div class="form-check form-check-inline" style="margin-bottom: 5px;">
                                        <input class="form-check-input" type="checkbox" name="en_alquiler" value="1">
                                        <label class="form-check-label">Alquiler</label>
                                    </div>
                            
                                
                                <button type="submit" class="btn-equipo" style="margin-top:25px;" >
                                    ENVIAR 
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
<!--FIN EQUIPO-->
@endsection
