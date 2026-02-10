@extends('layouts.plantilla')

@section('title','Videos')

@section('content')


<section class="section-videos">
    <div class="container">
        <div class="row">
            @foreach ($videos as $video)
                @if ($video->show)

                    <div class="col-12 col-md-4 video" >
                        <iframe width="100%" height="169px"src="https://www.youtube.com/embed/{{$video->link}}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        
                        <h5>{{$video->titulo}}</h5>
                        <p>{{$video->subtitulo}}</p>

                    </div>

                @endif
            @endforeach
        <div>
    </div>

</section>

@endsection