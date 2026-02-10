@extends('layouts.plantilla')

@section('title','Productos')

@section('content')

<!--INICIO SECCIÓN PRODUCTOS-->        
<section class="section-nuestros-productos">
    <div class="container">
        <div class="row">
            @foreach ($categorias as $categoria)

                @if ($categoria->show)

                    <div class="col-12 col-md-4">
                        <a href="{{ route('web.productos.categoria',$categoria) }}">
                            <div class="img-border-grey img-active" style="background-image: url({{asset(Storage::url($categoria->imagen))}}); ">
                            </div>
                        </a>
                        <p class="nombre-producto" >{{$categoria->name}}</p>
                    </div>
                @endif

            @endforeach
        </div>
    </div>
</section>

<!--FIN SECCIÓN PRODUCTOS-->        

@endsection