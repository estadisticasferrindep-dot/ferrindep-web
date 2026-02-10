@extends('layouts.plantilla')

@section('title','Quienes somos')

@section('content')


<!--INICIO CAROUSEL-EMPRESA-->

<div id="carouselExampleIndicators" class="carousel carousel-home carousel-empresa slide d-none d-md-block" data-bs-ride="carousel">
    
    <div class="carousel-indicators">
    @foreach ($imagenes as $key=>$imagen)    
                <button type="button" data-bs-target="#carouselExampleIndicators" class="{{$loop->first ? 'active' : ''}} btn-carousel" data-bs-slide-to="{{$key}}" {{$loop->first ? 'aria-current="true"' : ''}}  aria-label="Slide {{$key}}"></button>
    @endforeach
    </div>

    <div class="carousel-inner">
            @foreach ($imagenes as $imagen)
                    <div class="carousel-item {{$loop->first ? 'active' : ''}} " style="background-image: url({{asset(Storage::url($imagen->imagen))}})">
                        
                        <div class="carousel-overlay"></div>
                        
                        <div class="carousel-caption d-none d-md-block carousel-texto">
                            <h2 class="texto-empresa">{!!$imagen->texto!!}</h2>
                        </div>
                    </div>
            @endforeach
    </div>
</div>


<!--FIN CAROUSEL-EMPRESA-->

            <section class="section-empresa ">
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-md-6 parrafo-izquierda" style="padding-right: 20;">
                            
                            {!!$empresa->parrafo!!}
                            
                        </div>
                        <div class="col-12 col-md-6 imagen-empresa" style="background-image: url({{asset(Storage::url($empresa->imagen))}}); min-height: 340px;"></div>
                    
                    </div>
                </div>
            </section>


@endsection


            