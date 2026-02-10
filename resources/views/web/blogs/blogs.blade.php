@extends('layouts.plantilla')

@section('title','Post Venta')

@section('content')


<!--INICIO BLOG-->


<section class="section-home-noticias" style="margin-top:42px"> 
    <div class="container">
        <div class="row">

            
            @if ($filtro == 'todas')
                @foreach ($noticias as $noticia)
                    
                    @if ($noticia->destacar && $noticia->show)
                    <div class="col-12 col-md-6 item-noticia">
                        <div>
                            <div class="img-border-noticias" style="background-image: url({{asset(Storage::url($noticia->imagen))}}); "></div>
                            <h4>{{$noticia->categoria->nombre}}</h4>
                            <h5>{{$noticia->titulo}}</h5>

                            <p class="nombre-noticia" >{{$noticia->descripcion}}</p>
                        </div>
                        <a  href="{{ route('web.blogs.noticia',$noticia) }}" style="text-decoration: none"><button class="btn-noticias">LEER MÁS</button></a>
                    </div>

                    @else
                        @if ($noticia->show)
                        <div class="col-12 col-md-6 d-none d-md-flex" style="margin-top:67px;">
                            <div class="container item-blog">
                                <div class="row">
                                    <div class="img-border-blog col-4" style="background-image: url({{asset(Storage::url($noticia->imagen))}}); ">
                                    </div>
                                    <div class="col-8 item-blog-text">
                                        <h4>{{$noticia->categoria->nombre}}</h4>
                                        <h5>{{$noticia->titulo}}</h5>
                
                                        <p class="nombre-noticia" >{{$noticia->descripcion}}</p>
                                        
                                        <a href="{{ route('web.blogs.noticia',$noticia) }}" class="btn-blog">LEER MÁS</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endif
                
                @endforeach
                    
            @else
                @foreach ($noticias as $noticia)
                    @if ($noticia->claseblog_id == $filtro && $noticia->show)
                    <div class="col-12 col-md-6 item-noticia ">
                        <div>
                            <div class="img-border-noticias" style="background-image: url({{asset(Storage::url($noticia->imagen))}}); "></div>
                            <h4>{{$noticia->titulo}}</h4>
                            <h5>{{$noticia->subtitulo}}</h5>

                            <p class="nombre-noticia" >{{$noticia->descripcion}}</p>
                        </div>
                        <a  href="{{ route('web.blogs.noticia',$noticia) }}" style="text-decoration: none"><button class="btn-noticias">LEER MÁS</button></a>
                    </div>
                    @endif
                @endforeach
            @endif

        </div>
    </div>
</section>



<!--FIN BLOG-->
@endsection


            