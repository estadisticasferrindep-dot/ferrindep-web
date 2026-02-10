@extends('layouts.plantilla')

@section('title','Contacto')

@section('content')

<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3280.8501345328723!2d-58.41854558519235!3d-34.68373166924234!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x95bccc8a38d8cf11%3A0x17279d842436377c!2sCAM%20CNC%20%7C%20Pantografos!5e0!3m2!1ses!2sar!4v1617200960245!5m2!1ses!2sar" width="100%" height="332" style="border:0;" allowfullscreen="" loading="lazy"></iframe>

<section class="section-contacto">
    <div class="container">
        
        <form method="POST" action="{{route('web.contactanos')}}"> 
            @csrf
            <div class="row">
                <div class="col-4">
                    <div class="item-contact">
                        <i class="fas fa-map-marker-alt"></i>
                        <p>{{$configuracion->direccion}}</p>
                    </div>
                    
                    <div class="item-contact">
                            <i class="far fa-envelope"></i>
                            <p>{{$configuracion->email}}</p>
                    </div>
                        
                    <div class="item-contact">
                        <!-- Icono en SVG porque no estaba disponible en Font Awesome -->
                        <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"width="13px" height="13px" viewBox="0 0 349.325 349.324" style="enable-background:new 0 0 349.325 349.324;"xml:space="preserve">
                            <g>
                                <path d="M18.451,33.056C-8.6,73.651-6.972,151.824,42.83,207.313c46.215,51.491,115.158,108.634,115.735,109.101
                                    c1.478,1.341,36.774,32.91,88.89,32.91c5.043,0,10.161-0.31,15.214-0.919c56.533-6.83,77.256-43.071,84.579-64.059
                                    c3.782-10.801-2.585-24.196-13.914-29.254L266.985,225.6c-3.184-1.411-6.992-2.158-11.015-2.158c-8.2,0-16.432,3.042-21.47,7.937
                                    l-20.911,20.262c-3.107,3.011-8.627,5.032-13.746,5.032c-2.188,0-4.118-0.386-5.57-1.122c-14.116-7.079-36.3-21.211-61.857-48.307
                                    c-22.681-24.07-33.456-40.568-38.506-50.173c-2.821-5.373,0.127-14.678,4.552-19.096l18.603-18.596
                                    c7.734-7.734,11.217-21.962,7.924-32.39L104.07,20.556C100.887,10.453,90.149,0,77.629,0C60.288,0.584,35.942,6.795,18.451,33.056
                                    z M78.292,11.842c5.979,0.025,12.688,6.614,14.472,12.279l20.921,66.43c1.976,6.249-0.366,15.8-5.001,20.444l-18.606,18.59
                                    c-7.599,7.6-12.093,22.673-6.66,32.989c5.39,10.248,16.765,27.729,40.37,52.783c26.743,28.351,50.196,43.259,65.158,50.77
                                    c3.067,1.544,6.846,2.356,10.903,2.356c8.231,0,16.655-3.199,21.978-8.358l20.91-20.256c4.164-4.037,14.062-5.825,19.419-3.453
                                    l66.354,29.492c5.058,2.25,9.45,9.075,7.551,14.508c-6.413,18.393-24.663,50.15-74.804,56.214
                                    c-4.58,0.553-9.221,0.827-13.802,0.827c-46.996,0-79.704-28.741-81.133-30.011c-0.67-0.554-69.292-57.498-114.676-108.06
                                    C7.081,149.737,3.598,76.686,28.302,39.619C43.002,17.562,63.591,12.34,78.292,11.842z"/>
                            </g>
                        </svg>
                        <p>{{$configuracion->tel_celular}}</p>
                    </div>
        
                    <div class="item-contact">
                        <i class="fab fa-whatsapp"></i>
                        <p>{{$configuracion->wsp}}</p>
                    </div>
                </div>
        
                <div class="col-4">
                    <input class="box" name="nombre" placeholder="Nombre" style="margin-bottom:28px;">
                    <input class="box" name="email" placeholder="Email">
                    <div> 
                        <textarea class="box" name="comentarios" rows="9" style="padding-top:19px;">Comentaraios</textarea>
                    </div>
                    <button type="submit" class="borde-info orange-button button" style="margin-top:5px;" >
                        <p>ENVIAR</p>
                    </button>
                </div>
        
                <div class="col-4">
                    <input class="box" name="apellido" placeholder="Apellido" style="margin-bottom:28px;">
                    <input class="box" name="empresa" placeholder="Empresa">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="accept" value="1">
                        <label class="form-check-label">Acepto los términos y condiciones de privacidad</label>
                    </div>
                    <div class="g-recaptcha" data-sitekey="6LdBg5IUAAAAALr4Ygo1gsuo_DDsonFBf2w1jg2Y" style="margin-top:15px;"></div>
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