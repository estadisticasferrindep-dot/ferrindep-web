@extends('layouts.plantilla')

@section('title','Solicitud de Presupuesto')

@section('content')


<section class="section-solicitud-de-presupuesto">
    <div class="container">
        <form method="POST" action="{{route('web.presupuesto')}}"> 
            @csrf

            <div class="datos-personales">
                <h4> datos personales </h4>
                <div class="row">
                    <div class="col-6"><input class="box" name="nombre" placeholder="Nombre*"></div>
                    <div class="col-6"><input class="box" name="apellido" placeholder="Apellido*"></div>  
                </div>
                
                <div class="row">
                    <div class="col-6"><input class="box" name="email" placeholder="Email*"></div>
                    <div class="col-6"><input class="box" name="razon_social" placeholder="Razón social*"></div>  
                </div>
    
                <div class="row">
                    <div class="col-6"><input class="box" name="direccion" placeholder="Dirección*"></div>
                    <div class="col-6"><input class="box" name="telefono" placeholder="Teléfono*"></div>  
                </div>
            </div>
            
            <div class="row">
                <div class="col-3">
                    <h4> Ancho util de corte requerido* </h4>

                    <div class="form-check">
                        <input class="form-check-input" value="1.500" type="radio" name="ancho_util" id="ancho_util1">
                        <label class="form-check-label"  for="ancho_util1">1.500 mm</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" value="2.500" type="radio" name="ancho_util" id="ancho_util2">
                        <label class="form-check-label"  for="ancho_util2">2.500 mm</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" value="3.500" type="radio" name="ancho_util" id="ancho_util3">
                        <label class="form-check-label"  for="ancho_util3">3.500 mm</label>
                    </div>
                </div>

                <div class="col-3" style="padding-left: 0">
                    <h4> Largo útil de corte requerido* </h4>

                    <div class="form-check">
                        <input class="form-check-input" value="3.000" type="radio" name="largo_util" id="flexRadioDefault1">
                        <label class="form-check-label"  for="flexRadioDefault1">3.000 mm</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" value="6.000" type="radio" name="largo_util" id="flexRadioDefault2">
                        <label class="form-check-label"  for="flexRadioDefault2">6.000 mm</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" value="12.000" type="radio" name="largo_util" id="flexRadioDefault3">
                        <label class="form-check-label"  for="flexRadioDefault3">12.000 mm</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" value="15.000" type="radio" name="largo_util" id="flexRadioDefault4">
                        <label class="form-check-label"  for="flexRadioDefault4">15.000 mm</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" value="24.000" type="radio" name="largo_util" id="flexRadioDefault5">
                        <label class="form-check-label"  for="flexRadioDefault5">24.000 mm</label>
                    </div>
                </div>

                <div class="col-3" style="padding-left: 0">
                    <h4> Espesor de corte </h4>
                    <input type="number" class="box" name="minimo" placeholder="Mínimo" style="margin-bottom:28px;">
                    <input type="number" class="box" name="maximo" placeholder="Máximo">
                </div>

                <div class="col" style="padding-left: 0; padding-right:0;">
                    <h4> Material a cortar* </h4>

                    <div class="form-check" style="margin-bottom: 5px;">
                        <input class="form-check-input" type="checkbox" name="acrilico" value="1">
                        <label class="form-check-label">ACRILICO</label>
                    </div>
                    <div class="form-check" style="margin-bottom: 5px;">
                        <input class="form-check-input" type="checkbox" name="carton" value="1">
                        <label class="form-check-label">CARTON</label>
                    </div>
                    <div class="form-check" style="margin-bottom: 5px;">
                        <input class="form-check-input" type="checkbox" name="mdf" value="1">
                        <label class="form-check-label">MDF</label>
                    </div>
                    <div class="form-check" style="margin-bottom: 5px;">
                        <input class="form-check-input" type="checkbox" name="contrachapadp" value="1">
                        <label class="form-check-label">CONTRACHAPADO</label>
                    </div>
                </div>
                <div class="col">
                    <h4 style="color:transparent"> Material a cortar* </h4>

                    <div class="form-check" style="margin-bottom: 5px;">
                        <input class="form-check-input" type="checkbox" name="acrilico" value="1">
                        <label class="form-check-label">ACRILICO</label>
                    </div>
                    <div class="form-check" style="margin-bottom: 5px;">
                        <input class="form-check-input" type="checkbox" name="carton" value="1">
                        <label class="form-check-label">CARTON</label>
                    </div>
                    <div class="form-check" style="margin-bottom: 5px;">
                        <input class="form-check-input" type="checkbox" name="mdf" value="1">
                        <label class="form-check-label">MDF</label>
                    </div>
                    <div class="form-check" style="margin-bottom: 5px;">
                        <input class="form-check-input" type="checkbox" name="contrachapadp" value="1">
                        <label class="form-check-label">CONTRACHAPADO</label>
                    </div>
                </div>
            </div>
            
            <div class="row" style="margin-top:23px;">
                <div class="col-6"> 
                    <textarea class="box" name="comentarios" >Comentarios</textarea>
                </div>
                <div class="col-3"></div>
                <div class="col-3" style="display: flex; align-items:flex-end">
                    <button type="submit" class="borde-info button" style="margin-top:35px; margin-left:25px;" >
                        Enviar solicitud
                    </button>

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