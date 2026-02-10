@extends('layouts.plantilla')

@section('title','Resultados')

@section('content')

<!--INICIO SECCIÓN PRODUCTOS-->        

<section class="section-categoria" style="margin-bottom:65px; margin-top:87px;">

    <div class="container">
        <div class="row">
            <section class="section-home-categorias col-12" style="margin-bottom: 24px"> 
                <div class="container-fluid">
                    <div class="row">

                        @foreach ($productos as $item)
                        
                                @if ($item->show)
                                    {{-- @if (!$item->hay_stock)
                                        <div class="col-12 col-md-4" >
                                            <a href="{{ route('web.productos.producto',$item) }}" style="text-decoration: none">
                                            <div class="img-border-categorias img-active" style="background-image: url({{asset(Storage::url($item->imagen))}}); ">
                                                <div class="ribbon ribbon-top-left sin_stock"><span style="    background-color: #6c757d;">SIN STOCK</span></div>
                                            </div>
                                                
                                            <div class="text-box-categorias">
                                                <h4>{{$item->nombre}}</h4>
                                                <p class="precio" style="text-align: right;">${{$item->tprecio}}</p>
                                            </div> 
                                            </a>
                                            
                                        </div>
                                    @else --}}
                                        <div class="col-12 col-md-4" >
                                            <div style="border: 1px solid rgb(143, 134, 110, 0.3); padding:12px; width:100%; height:100%;">
                                                <a href="{{ route('web.productos.producto',$item) }}" style="text-decoration: none">
                                                    <div class="img-border-categorias img-active" style="background-image: url({{asset(Storage::url($item->imagen))}}); ">
                                                        {{-- <div class="ribbon ribbon-top-left"><span>¡OFERTA!</span></div> --}}
                                                        @if($item->oferta)  <div class="oferta">OFERTA</div> @endif
                                                    </div>
                                                    </a>    
                                                    <div class="text-box-categorias">
                                                        <h4>{{$item->nombre}}</h4>
                                                        <p class="descripcion">{{$item->descripcion}}</p>
                                                        <hr>
                                                        <p style="font: normal normal 300 14px/17px Rubik;">Colores</p>
                                                        
                                                        <div style="display: flex">
                                                            @foreach ($item->colores as $colorItem)
        
                                                        
                                                                <div class="cuadrado-color" style="background-image: url({{asset(Storage::url($colorItem->color->imagen))}}); "></div>
                                                            @endforeach
                                                        </div>
        
        
                                                        <hr>
                                                        {{-- <p class="precio" style="text-align: center;"><span class="precio-oferta">${{$item->precio_anterior}}</span>${{$item->precio}}</p> --}}
                                                        
                                                        <diametros-categorias diametros="{{ $item->diametros }}" oferta="{{$item->oferta}}" price="{{$item->precio}}" precio-anterior="{{$item->precio_anterior}}"
                                                            ref="galeria"
                                                        />
        
                                                    
                                                    </div> 
                                                </a>
                                            </div>
                                            
                                        </div>

                                            {{-- <div class="col-12 col-md-4" style="border: 1px solid rgb(143, 134, 110, 0.3); padding-top:12px;" >
                                                <a href="{{ route('web.productos.producto',$item) }}" style="text-decoration: none">
                                                    <div class="img-border-categorias img-active" style="background-image: url({{asset(Storage::url($item->imagen))}}); "></div>
                                                </a>        
                                                <div class="text-box-categorias">
                                                    <h4>{{$item->nombre}}</h4>
                                                    <p class="descripcion">{{$item->descripcion}}</p>
                                                    <hr>
                                                    <p style="font: normal normal 300 14px/17px Rubik;">Colores</p>
                                                    
                                                    <div style="display: flex">
                                                        @foreach ($item->colores as $colorItem)
    
                                                    
                                                            <div class="cuadrado-color" style="background-image: url({{asset(Storage::url($colorItem->color->imagen))}}); "></div>
                                                        @endforeach
                                                    </div>
    
    
                                                    <hr>
                                                    <diametros-categorias diametros="{{ $item->diametros }}" oferta="{{$item->oferta}}"
                                                        ref="galeria"
                                                    />
                                                    <p class="precio" style="text-align: center;">${{$item->precio}}</p>
                                                
                                                </div> 
                                                
                                            </div>   
                                        
                                    @endif --}}


                                    
                                @endif
                        @endforeach
            
                    </div>
                </div>
            </section>
        </div>
    </div>

</section>

<!--FIN SECCIÓN PRODUCTOS-->        

@endsection