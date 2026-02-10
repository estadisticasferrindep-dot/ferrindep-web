@extends('layouts.plantilla')

@section('title','Servicios')

@section('content')


<!--INICIO SERVICIOS-->


            <section class="section-servicios ">
                <div class="container">
                    <div class="row">

                        @foreach ($servicios as $servicio)

                        <div class="col-12 col-md-4" style="display: flex; justify-content: center; flex-direction:column; align-items:center; margin-bottom:25px;">
                            <div class="imagen" style=" background-image: url( {{ asset(Storage::url($servicio->imagen)) }} ); "> <div class="overlay"></div></div>
                            <div class="imagen-text"  style="display: flex; justify-content: center; flex-direction:column; align-items:center">
                                
                                <div class="text-servicio"  >
                                    <h4>{{$servicio->titulo}}</h4>
                                    
                                    <p>{{$servicio->descripcion}}</p>
                                </div>

                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </section>


        

            
<!--FIN POST VENTA-->
@endsection


            