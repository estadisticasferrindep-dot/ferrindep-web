@extends('layouts.plantilla')

@section('title','Productos')

@section('content')

<!--INICIO SECCIÓN PRODUCTOS-->        

<section class="section-home-categorias" style="margin-bottom: 24px"> 
            
            
    <div class="container">
        <div class="row">

            @foreach ($categorias as $categoria)

                @if ($categoria->show)

                <div class="col-12 col-md-3 " style="margin-bottom:38px; margin-top:38px; ">
                    <a href="{{ route('web.productos.categoria',$categoria) }}x" style="text-decoration: none">
                    <div class="img-border-categorias img-active" style="background-image: url({{asset(Storage::url($categoria->imagen))}}); "></div>
                        
                    <div class="text-box-categorias">
                        <h4 > {{intval($item->nombre) >= 100 ? $item->nombre/100 .' m' : $item->nombre .' cm'  }} alto/ancho </h4>
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