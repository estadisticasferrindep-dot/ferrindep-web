@extends('layouts.plantilla')

@section('title','Preguntas Frecuentes')

@section('content')


<section class="section-contacto section-login" style="margin-top:40px;">
    <div class="container">

        {{-- <form method="POST" enctype="multipart/form-data"  action="{{route('web.contactanos')}}"> 
            @csrf --}}
            <div class="row">
                <div class="col-12 col-md-6" style="border-left: 1px solid lightgray;">
                    <registro target="{{route('web.clientes.register')}}" ref="registro" />
                </div>

                <div class="col-12 col-md-6" style="margin-bottom: 40px;">
                    <login target="{{route('web.clientes.login')}}" ruta="{{route('web.carrito')}}" ref="login" />
                </div>
               
            </div>
        
    </div>

</section>

@endsection