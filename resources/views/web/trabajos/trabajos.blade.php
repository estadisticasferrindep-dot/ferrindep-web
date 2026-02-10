@extends('layouts.plantilla')

@section('title','Trabajos')

@section('content')

<!--INICIO SECCIÓN PRODUCTOS-->        

        <section class="section-home-categorias" style="margin-bottom: 24px"> 
            
            
            <div class="container">
                <div class="row">

                    @foreach ($trabajos as $trabajo)

                        @if ($trabajo->show)

                        <div class="col-12 col-md-3 " style="margin-bottom:38px ">
                            <a href="{{ route('web.trabajos.trabajo',$trabajo) }}" style="text-decoration: none">
                            <div class="img-border-categorias img-active" style="background-image: url({{asset(Storage::url($trabajo->imagen))}}); "></div>
                                
                            <div class="text-box-categorias">
                                <h4 >{{$trabajo->nombre}}</h4>
                            </div> 
                            </a>
                        </div>

                            
                        @endif
                    @endforeach

                </div>
            </div>
        </section>

<!--FIN SECCIÓN PRODUCTOS-->        

@endsection