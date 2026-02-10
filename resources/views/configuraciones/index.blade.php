@extends('layouts.app')

@section('title','Configuración')

@section('content')


<div class="container cont-configuracion">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif


                    <?php $configuracion = $configuraciones->first() ?>
            
                        
                    <form action="{{route('configuraciones.update',$configuracion)}}" enctype="multipart/form-data" method="POST">
                                
                        @csrf
                        
                        @method('put')
                        
                         <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Direccón</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="fas fa-map-marker-alt"></i></span>
                                    </div>
                                    <input type="text" name="direccion" value="{{old('direccion',$configuracion->direccion)}}" class="form-control"  placeholder="Direccón">
                                </div>
                            </div> 
                            <div class="form-group col-md-6">
                                <label>Email </label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="far fa-envelope"></i></span>
                                    </div>
                                    <input type="email" name="email" value="{{old('email',$configuracion->email)}}" class="form-control"  placeholder="Email">
                                </div>
                            </div>  
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Teléfono</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="fas fa-phone-alt"></i></span>
                                    </div>
                                    <input type="text" name="tel" value="{{old('tel',$configuracion->tel)}}"  class="form-control"  placeholder="Teléfono">
                                </div>
                            </div> 
                            <div class="form-group col-md-6">
                                <label>WhatsApp</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="fab fa-whatsapp"></i></span>
                                    </div>
                                    <input type="tel" name="wsp" value="{{old('wsp',$configuracion->wsp)}}" class="form-control"  placeholder="WhatsApp">
                                </div>
                            </div>  
                        </div>
                
                        <div class="form-check ">
                            <input class="form-check-input" type="checkbox" {{ old('wsp_show', $configuracion->wsp_show) == 1 ? 'checked' : '' }} name="wsp_show" value="1">
                            <label class="form-check-label">Mostrar WSP</label>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Iframe del mapa</label>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"> <i class="fas fa-map-marker-alt"></i></span>
                                </div>
                                <input type="text" name="iframe" value="{{old('iframe',$configuracion->iframe)}}"  class="form-control">
                            </div> 
                        </div>

                       

                        <button type="submit" class="btn btn-primary mb-2" >Actualizar Configuración</button>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>


@endsection