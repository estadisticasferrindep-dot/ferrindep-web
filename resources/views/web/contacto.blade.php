@extends('layouts.plantilla')

@section('title','Contacto')

@section('content')


<section class="section-contacto">
    
{!!$configuracion->iframe!!}
    <div class="container" style="padding-top:60px;">
        
        <form method="POST" enctype="multipart/form-data"  action="{{route('web.contactanos')}}"> 
            @csrf
            <div class="row">
                <div class="col-12 col-md-4">
                    
                    <div class="item-contact">
                        <i class="fas fa-map-marker-alt" style="margin-top:3px;"></i>
                        <p>{{$configuracion->direccion}}</p>
                    </div>
                    
                    <div class="item-contact">
                        <i class="fas fa-phone-alt"></i>
                        <p>{{$configuracion->tel}}</p>
                    </div>
                    <div class="item-contact">
                        <i class="fab fa-whatsapp" style="font-size:18px;"></i>
                        <p>{{$configuracion->wsp}}</p>
                    </div>
                    <div class="item-contact">
                            <i class="fas fa-envelope" style="margin-top:2px;"></i> 
                            <p>{{$configuracion->email}}</p>
                    </div>
                </div>
        
                <div class="col-12 col-md-8" >
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <input class="box" name="nombre" placeholder="Nombre" style="margin-bottom:14px;">
                            <input class="box" name="email" placeholder="Email">
                        
                        </div>
                
                        <div class="col-12 col-md-6">
                            <input class="box" name="apellido" placeholder="Apellido" style="margin-bottom:14px;">
                            <input class="box" name="celular" placeholder="Celular">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12"> 
                            <textarea class="box" name="mensaje" rows="6" style="padding-top:19px;">Mensaje</textarea>
                        </div>
                    </div>
                    <div class="row">
                        {{-- <div class="col-6">
                            <a href="#" style="text-decoration: none">
                                <div  class="input-descarga " >
                                <span>Examinar Archivo</span>
                                <span>...</span>
                                </div>
                            </a>
                        </div> --}}

                        <div class="col-5 col-md-4" style="display: flex;">
                            <button type="submite" class="contacto-btn col-3" >
                            Enviar 
                            </button>
                        </div>
                    </div>
                </div>

                
            </div>


        </form>   
        
        @if (session('info'))
            <script>
                alert("{{ session('info') }}");
            </script>
        @endif
    </div>

</section>

@endsection