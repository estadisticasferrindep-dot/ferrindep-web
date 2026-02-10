@extends('layouts.plantilla')

@section('title','Preguntas Frecuentes')

@section('content')


<section class="section-preguntas">
    <div class="container">

        
        <div class="accordion accordion-flush" id="accordionFlushExample">
            @foreach ($preguntas as $i => $pregunta)
                @if ($pregunta->show)
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="flush-heading{{$i}}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse{{$i}}" aria-expanded="false" aria-controls="flush-collapse{{$i}}">
                                <div class="icon-border"><div class="icon"></div></div> {{$pregunta->titulo}}
                            </button>
                        </h3>
                        <div id="flush-collapse{{$i}}" class="accordion-collapse collapse" aria-labelledby="flush-heading{{$i}}" data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">{!!$pregunta->parrafo!!}</div>
                        </div>
                    </div>

                @endif
            @endforeach
        </div>
    </div>

</section>

@endsection