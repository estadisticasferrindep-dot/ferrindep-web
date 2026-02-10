@extends('layouts.plantilla')

@section('title','Ofertas')

@section('content')

<!--INICIO SECCIÓN PRODUCTOS-->        

<section class="section-productos" style="margin-block: 55px;margin-top:42px;">
    <div class="container">
        <div class="row">
            @foreach ($productos as $itemProducto)
            @if ($itemProducto->show)
                <div class="col-12 col-md-4 item-producto" style="margin-bottom: 37px;">
                    <a href="{{ route('web.productos.producto',$itemProducto) }}" style="text-decoration: none">
                        <div class="box-clase img-active">
                            <div class="img-border-equipo" style="background-image: url({{asset(Storage::url($itemProducto->imagen))}}); "></div>
                        </div>
                    </a>
                    
                    <h3>{{$itemProducto->nombre}}</h3>
                    <p>{{$itemProducto->modelo}}</p>
                </div>
            @endif
        @endforeach
        </div>
    </div>   
    </div>
    
</section>

<!--FIN SECCIÓN PRODUCTOS-->        

@endsection