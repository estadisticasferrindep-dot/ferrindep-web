@extends('layouts.plantilla')

@section('title','Clientes')

@section('content')


<section class="section-clientes">
    <div class="container">
        <div class="row">
            @foreach ($clientes as $cliente)
                @if ($cliente->show)

                    <div class="col-12 col-md-2">
                        <div class="img-border-grey  " style="background-image: url({{asset(Storage::url($cliente->imagen))}}); margin-bottom:26px;">
                        </div>
                    </div>

                @endif
            @endforeach
        <div>
    </div>

</section>

@endsection