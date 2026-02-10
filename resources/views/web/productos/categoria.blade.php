@extends('layouts.plantilla')

@section('title', $categoria->con_nombre ? $categoria->nombre :  $categoria->nombre . 'cm de ancho')

@section('content')

<!--INICIO SECCIÓN PRODUCTOS-->        

<section class="section-categoria" style="margin-bottom:65px; margin-top:87px;">

    <div class="container">
        <div class="row">

            <section class="nav-categorias col-12 col-md-3" style="margin-bottom:70px;">
                
                <div class="list-group list-group-flush">
                    @foreach ($familias as $familia)
                    <a href="{{route('web.productos.productos2', $familia->id)}}" style="color: #FD914D !important;padding-left: 0; font-weight: 400;display: flex;
    justify-content: space-between;" class=" d-none d-md-flex cat-no-activa  list-group-item list-group-item-action list-caracteristica"> {{$familia->nombre}}  <i class="fas fa-angle-down"></i></a>
    <a href="{{route('web.productos.productos2.mobile', $familia->id)}}"  style="color: #FD914D !important;padding-left: 0; font-weight: 400;display: flex;
    justify-content: space-between;" class=" d-flex d-md-none cat-no-activa  list-group-item list-group-item-action list-caracteristica"> {{$familia->nombre}}  <i class="fas fa-angle-down"></i></a>

                        @if ($familia->id == $familiaElegida)
                            @foreach ($categorias as $item)
                                @if ($item->show && $item->tieneProductosFamilia($familia->id))
                                    <a href="{{route('web.productos.categoria', [$item->id,$familia->id])}}" class=" d-none d-md-flex cat-no-activa  list-group-item list-group-item-action list-caracteristica"> {{$item->con_nombre ? $item->nombre : (intval($item->nombre) >= 100 ? $item->nombre/100 .' m alto/ancho' : $item->nombre .' cm alto/ancho')  }}</a>
                                    <a href="{{route('web.productos.categoria.mobile', [$item->id,$familia->id])}}" class=" d-flex d-md-none cat-no-activa  list-group-item list-group-item-action list-caracteristica"> {{$item->con_nombre ? $item->nombre : (intval($item->nombre) >= 100 ? $item->nombre/100 .' m alto/ancho' : $item->nombre .' cm alto/ancho')  }}</a>

                                @endif

                                @foreach ($productos as $item2)
                                    @if ($item2->show && $item->id == $item2->categoria_id && $item2->familia_id == $familiaElegida )
                                    @if ($item2->con_nombre)
                                        <a  href="{{ route('web.productos.producto',$item2->id) }}" class="{{ false ? 'prod-activo ' : 'prod-no-activo '}}list-group-item d-none d-md-flex list-group-item-action list-trabajo" style=" padding-left:35px;" >{{$item2->nombre}} </a>
                                        <a  href="{{ route('web.productos.producto.mobile',$item2->id) }}#prod" class="{{ false ? 'prod-activo ' : 'prod-no-activo '}}list-group-item  d-flex d-md-none list-group-item-action list-trabajo" style=" padding-left:35px;" >{{$item2->nombre}} </a>
                                    
                                        @else
                                        <a  href="{{ route('web.productos.producto',$item2->id) }}" class="{{ false ? 'prod-activo ' : 'prod-no-activo '}}list-group-item d-none d-md-flex  list-group-item-action list-trabajo" style=" padding-left:35px;" >{{$item2->medidas->medidas}} {{$item2->espesor->espesor}} </a>
                                        <a  href="{{ route('web.productos.producto.mobile',$item2->id) }}" class="{{ false ? 'prod-activo ' : 'prod-no-activo '}}list-group-item d-flex d-md-none list-group-item-action list-trabajo" style=" padding-left:35px;" >{{$item2->medidas->medidas}} {{$item2->espesor->espesor}} </a>
                                    
                                        @endif

                                    @endif
                                @endforeach 
                            @endforeach

                            
                        @endif
                    @endforeach
                </div>
            </section>
            <section class="section-home-categorias col-12 col-md-9" style="margin-bottom: 24px" id="mobile"> 
                <div class="container-fluid">
                    <div class="row">

                        @foreach($productos as $item)
                        
                                @if($item->show && $item->familia_id == $familiaElegida && $item->categoria_id == $categoria_id )
                                    

                                <div class="d-none d-md-flex col-md-3" style="margin-bottom:40px;">
                                        <div style="border: 1px solid rgb(143, 134, 110, 0.3); padding:8px; width:100%; height:100%;">
                                            <a href="{{ route('web.productos.producto',$item) }}" style="text-decoration: none">
                                            <div class="img-border-categorias img-active" style="background-image: url({{asset(Storage::url($item->imagen))}}); ">
                                                
                                                {{-- <div class="ribbon ribbon-top-left"><span>¡OFERTA!</span></div> --}}
                                                @if($item->oferta)  
                                                
                                                
                                                <div class="oferta">OFERTA</div> @endif
                                            </div>
                                            <hr>

                                            <div class="text-box-categorias">
                                                <div class="tabla-trabajo" style="font: normal normal bold 12px/17px Open Sans;
                                                color: #939292;
                                                margin-top:16px;">
                                                <span style="font-size:120%">{{$item->categoria->con_nombre ? $item->categoria->nombre : (intval($item->categoria->nombre) >= 100 ? $item->categoria->nombre/100 .' m alto/ancho' : $item->categoria->nombre .' cm alto/ancho' ) }}</span> 
                                                </div>

                                                @if ($item->con_nombre)
                                                <div class="tabla-trabajo" style="font: normal normal bold 18px/28px Open Sans; color: black;" >
                                                    {{$item->nombre}}
                                                </div>
                                                @else
                                                <div class="tabla-trabajo" style="font: normal normal bold 18px/28px Open Sans; color: black;" >
                                                    {{$item->medidas->medidas}} {{$item->espesor->espesor}} 
                                                </div>
                                                @endif


                                                <hr>
                                                <desde-categorias 
                                                desc-efectivo="{{$configuracionPedidos->descuento_efectivo}}"
                                                desc-transferencia="{{$configuracionPedidos->descuento_transferencia}}"
                                                desc-mp="{{$configuracionPedidos->descuento_mp}}"
                                                
                                                vendidos="{{$item->vendidos}}" 
                                                presentaciones="{{ $item->presentaciones }}" 
                                                oferta="{{$item->oferta}}" 
                                                con-nombre="{{$item->con_nombre}}" 
                                                :zonas="{{ $zonas }}" :destinos="{{ $destinos }}" :destino-zonas="{{ $destinozonas }}"
                                                location-name="{{ $ubicacionCliente->cityName ?? $ubicacionCliente->regionName ?? '' }}"
                                                />
                                                
                                            </div> 
                                            </a>
                                        </div>
                                    </div>

                                    



                                    <div class="d-flex d-md-none col-12" style="margin-bottom:0px; padding:0;">
                                        <div style="border: 1px solid rgb(143, 134, 110, 0.3);     padding: 7px 0; width:100%; height:100%;">
                                            <a href="{{ route('web.productos.producto',$item) }}" style="text-decoration: none">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="img-border-categorias img-active" style="background-image: url({{asset(Storage::url($item->imagen))}}); ">
                                                    @if($item->oferta)  
                                                    
                                                    
                                                    <div class="oferta">OFERTA</div> @endif
                                                </div>

                                                </div>
                                                <div class="col-8">

                                                    <div class="text-box-categorias" style="    display: flex;
                                                    flex-direction: column;
                                                    height: 100%;
                                                    justify-content: space-between;">
                                                    

                                                    <div>
                                                        @if ($item->con_nombre)
                                                        <div class="tabla-trabajo" style="font: normal normal 600 16px/25px Open Sans; color: black;" >
                                                            {{$item->nombre}} 
                                                        </div>
                                                        @else
                                                        <div class="tabla-trabajo" style="font: normal normal 600 16px/25px Open Sans; color: black;" >
                                                            {{$item->medidas->medidas}} {{$item->espesor->espesor}} 
                                                        </div>
                                                        @endif


                                                        <div class="tabla-trabajo" style="font: normal normal bold 12px/17px Open Sans;
                                                        color: #939292;
                                                        margin-top:2px;">
                                                        <span style="font-size:120%;
                                                            font-style: italic;">{{$item->categoria->con_nombre ? $item->categoria->nombre : (intval($item->categoria->nombre) >= 100 ? $item->categoria->nombre/100 .' m ancho' : $item->categoria->nombre .' cm ancho' ) }}</span>                                              
                                                        </div>

                                                        </div>
                                                    <div style="font: normal normal normal 14px/16px Open Sans; color: #FD914D; margin-top: 8px;"> Click para ver mas detalles</div>
                                                    
                                                </div> 

                                                </div>
                                            </div>


                                            
                                            </a>
                                        </div>
                                    </div>
                                    

                                    
                                    
                                @endif
                        @endforeach
                    </div>
            
                    </div>
                </div>
            </section>
        </div>
    </div>

</section>
<!--FIN SECCIÓN PRODUCTOS-->        

@endsection