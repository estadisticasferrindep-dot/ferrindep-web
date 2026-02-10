@extends('layouts.plantilla')

@section('title','Post Venta')

@section('content')


<!--INICIO BLOG-->


<section class="section-blog-noticia"> 
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-8">
                <h4>{{$noticia->categoria->nombre}}</h4>
                <h5>{{$noticia->titulo}}</h5>
                <div class="img-border-noticia" style="background-image: url({{asset(Storage::url($noticia->imagen))}}); ">
                </div>
                <p>{!!$noticia->caracteristicas!!}</p>
            </div>

            <div class="col-12 col-md-4 blog-nav">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item ul-title">Blog</li>
                    <li class="list-group-item"><a href="{{ route('web.blogs','todas') }}" style="text-decoration: none">TODAS<span>><span></a></li>
                    @foreach ($claseblogs as $claseblog)
                    <li class="list-group-item"><a href="{{ route('web.blogs',$claseblog->id) }}" style="text-decoration: none">{{$claseblog->nombre}}<span>><span></a></li>
                        
                    @endforeach
                </ul>
            </div>
        </div>

    </div>

</section>



<!--FIN BLOG-->
@endsection


            