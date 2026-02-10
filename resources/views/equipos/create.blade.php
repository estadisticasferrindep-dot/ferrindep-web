@extends('layouts.app')

@section('title','Equipo Nuevo')

@section('content')


<div class="container cont-equipos">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <a style ="color:grey;" href="{{route('equipos.index')}}"><i class="fas fa-arrow-circle-left" style ="color:grey; margin-right:6px;"></i>Volver al listado de equipos</a>
            
            <div class="card" style="margin-top:15px;">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <form action="{{route('equipos.store')}}" enctype="multipart/form-data" method="POST">
                            @csrf

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Orden</label>
                                    <input type="text" name="orden" value="{{old('orden')}}" class="form-control" placeholder="Orden">
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Nombre</label>
                                    <input type="text" name="nombre" value="{{old('nombre')}}" class="form-control" placeholder="Nombre">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Modelo</label>
                                    <input type="text" name="modelo" value="{{old('modelo')}}" class="form-control" placeholder="Modelo">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Elige una clase para el equipo</label>
                                    <select class="form-control" name="clase_id">
                                        <option disabled>Elige una clase...</option>
                                        @foreach ($clases as $clase)
                                            <option {{ old('clase_id') == $clase->id ? 'selected' : '' }} value="{{$clase->id}}"> {{$clase->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Elige una altura de trabajo para el equipo</label>
                                    <select class="form-control" name="altura_id">
                                        <option disabled>Elige una altura de trabajo...</option>
                                        @foreach ($alturas as $altura)
                                            <option {{ old('altura_id') == $altura->id ? 'selected' : '' }} value="{{$altura->id}}"> {{$altura->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Elige un tipo de combustión para el equipo</label>
                                    <select class="form-control" name="combustion_id">
                                        <option disabled>Elige una un tipo de combustión...</option>
                                        @foreach ($combustiones as $combustion)
                                            <option {{ old('combustion_id') == $combustion->id ? 'selected' : '' }} value="{{$combustion->id}}"> {{$combustion->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="form-group">
                                <label>Imagen General</label>
                                <input type="file" accept="image/*" name="imagen_general" value="{{old('imagen_general')}}" class="form-control-file" >
                            </div>

                            <div class="form-group">
                                <label>Imagen Detalle</label>
                                <input type="file" accept="image/*" name="imagen_detalle" value="{{old('imagen_detalle')}}" class="form-control-file" >
                            </div>


                            <div class="form-group">
                                <label>Ficha técnica</label>
                                <input type="file" name="ficha_tecnica" value="{{old('ficha_tecnica')}}" class="form-control-file" >
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" {{ old('show') == 1 ? 'checked' : '' }} name="show" value="1">
                                <label class="form-check-label">Mostrar</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" {{ old('en_venta') == 1 ? 'checked' : '' }} name="en_venta" value="1">
                                <label class="form-check-label">En venta</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" {{ old('en_alquiler') == 1 ? 'checked' : '' }} name="en_alquiler" value="1">
                                <label class="form-check-label">En alquiler</label>
                            </div>

                            <div class="form-group ">
                                <label>Información detallada</label>
                                <textarea class="form-control summernote" name="tabla"  rows="3">{{old('tabla')}}</textarea>
                            </div>

                            {{-- <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Capacidad</label>
                                    <input type="text" name="capacidad" value="{{old('capacidad')}}" class="form-control" placeholder="Capacidad">
                                </div>

                                <div class="form-group col-md-6">
                                    <label> Medidas</label>
                                    <input type="text" name="medidas" value="{{old('medidas')}}" class="form-control" placeholder="Medidas">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Altura de Trabajo</label>
                                    <input type="text" name="altura_de_trabajo" value="{{old('altura_de_trabajo')}}" class="form-control" placeholder="Altura de Trabajo">
                                </div>

                                <div class="form-group col-md-6">
                                    <label> Altura de Plataforma</label>
                                    <input type="text" name="altura_de_plataforma" value="{{old('altura_de_plataforma')}}" class="form-control" placeholder="Altura de Plataforma">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Tamaño Global</label>
                                    <input type="text" name="tamaño_global" value="{{old('tamaño_global')}}" class="form-control" placeholder="Tamaño Global">
                                </div>

                                <div class="form-group col-md-6">
                                    <label> Min. distancia piso</label>
                                    <input type="text" name="min_distancia_piso" value="{{old('min_distancia_piso')}}" class="form-control" placeholder="Min. distancia piso">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Distancia entre ejes</label>
                                    <input type="text" name="distancia_entre_ejes" value="{{old('distancia_entre_ejes')}}" class="form-control" placeholder="Distancia entre ejes">
                                </div>

                                <div class="form-group col-md-6">
                                    <label> Velocidad Ascenso y descenso</label>
                                    <input type="text" name="velocidad_ascenso_descenso" value="{{old('velocidad_ascenso_descenso')}}" class="form-control" placeholder="Velocidad Ascenso y descenso">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Motor eléctrico</label>
                                    <input type="text" name="motor_electrico" value="{{old('motor_electrico')}}" class="form-control" placeholder="Motor eléctrico">
                                </div>

                                <div class="form-group col-md-6">
                                    <label> Batería</label>
                                    <input type="text" name="bateria" value="{{old('bateria')}}" class="form-control" placeholder="Batería">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Tamaño ruedas</label>
                                    <input type="text" name="tamaño_ruedas" value="{{old('tamaño_ruedas')}}" class="form-control" placeholder="Tamaño ruedas">
                                </div>

                                <div class="form-group col-md-6">
                                    <label> Peso Neto</label>
                                    <input type="text" name="peso_neto" value="{{old('peso_neto')}}" class="form-control" placeholder="Peso Neto">
                                </div>
                            </div> --}}

                            <button type="submit" class="btn btn-primary mb-2">Enviar Equipo</button>

                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
    
    


@endsection