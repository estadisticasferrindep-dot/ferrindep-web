@extends('layouts.plantilla')

@section('title', strtoupper($trabajo->nombre) )

@section('content')


<!--INICIO PRODUCTO-->


<section class="section-categoria" >

    <div class="container">
        <div class="row">

            <section class="nav-categorias col-2 d-none d-md-flex">
                <div class="list-group list-group-flush" style="width: 100%;">

                    @foreach ($trabajos as $item)
                    @if ($item->show )
                        <a  href="{{ route('web.trabajos.trabajo',$item) }}" class="list-group-item list-group-item-action list-trabajo" style="{{$trabajo->id == $item->id ? 'background-color:#E8E8E8; color: #726D6E;' : 'color: #C7D52B;'}}" >{{$item->nombre}}</a>

                    @endif

                @endforeach
                </div>
            </section>
            <section class="section-equipo col-10">
                <div class="container" style="padding-left:3px">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="">
                                <div class="img-trabajo" style="background-image: url({{asset(Storage::url($trabajo->imagen))}}); "></div>
                            </div>

                            <div class="container" style="padding-right:0; padding-left:0;">
                                <div class="row" style="padding-right: calc(var(--bs-gutter-x)/ 2);padding-left: calc(var(--bs-gutter-x)/ 2);">
                                    @foreach ($galerias as $item)
                                    <div class="box-clase-mini col-3" style="background-image: url({{asset(Storage::url($item->imagen))}}); ">
                                    <div class="overlay"></div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                        <div class="col-12 col-md-6" style="display: flex; flex-direction:column; justify-content:space-between">
                            <div>
                                <div class="tabla-trabajo">
                                    {!!$trabajo->tabla!!}
                                </div>
                            
                            </div>

                        </div>
                    </div>
                    
                    @foreach ($trabajo->secciones as $seccion)
                        @if ($seccion->show)
                            <div class="row"  style="padding-right: calc(var(--bs-gutter-x)/ 2);padding-left: calc(var(--bs-gutter-x)/ 2); margin-top:40px;">
                                <div class="col-12" style="background-color: #C7D52B; padding-top:5px;"><h5>{{$seccion->titulo}}</h5></div>
                            </div>
                            <div class="row"  style="padding-right: calc(var(--bs-gutter-x)/ 2);padding-left: calc(var(--bs-gutter-x)/ 2); margin-top:44px;">
                                <div class="col-12 descripcion"> {!!$seccion->texto!!} </div>
                            </div>
                        @endif
                    @endforeach


                    <div class="row"  style="padding-right: calc(var(--bs-gutter-x)/ 2);padding-left: calc(var(--bs-gutter-x)/ 2); display:flex; justify-content:center;">
                        <h5 class="col-12" style="font: normal normal bold 15px/20px Roboto Condensed;letter-spacing: 0.7px;color: #726D6E; margin-top:47px; margin-bottom:27px;">PRODUCTOS RELACIONADOS</h5>
                        
                        @if ($relacionado_1)
                        <div class="col-12 col-md-4">
                            <a href="{{ route('web.trabajos.trabajo',$relacionado_1) }}" style="text-decoration: none">
                            <div class="img-border-categorias img-active" style="background-image: url({{asset(Storage::url($relacionado_1->imagen))}}); "></div>
                                
                            <div class="text-box-categorias">
                                <h4 style="margin-top:22px; text-transform:uppercase;" >{{$relacionado_1->nombre}}</h4>
                            </div> 
                            </a>
                        </div>
                        @endif

                        @if ($relacionado_2)
                        <div class="col-12 col-md-4">
                            <a href="{{ route('web.trabajos.trabajo',$relacionado_2) }}" style="text-decoration: none">
                            <div class="img-border-categorias img-active" style="background-image: url({{asset(Storage::url($relacionado_2->imagen))}}); "></div>
                                
                            <div class="text-box-categorias">
                                <h4 style="margin-top:22px; text-transform:uppercase;" >{{$relacionado_2->nombre}}</h4>
                            </div> 
                            </a>
                        </div>
                        @endif

                        @if ($relacionado_3)
                        <div class="col-12 col-md-4">
                            <a href="{{ route('web.trabajos.trabajo',$relacionado_3) }}" style="text-decoration: none">
                            <div class="img-border-categorias img-active" style="background-image: url({{asset(Storage::url($relacionado_3->imagen))}}); "></div>
                                
                            <div class="text-box-categorias">
                                <h4 style="margin-top:22px; text-transform:uppercase;" >{{$relacionado_3->nombre}}</h4>
                            </div> 
                            </a>
                        </div>
                        @endif

                    </div>

                </div>
            </section>
        </div>
    </div>

</section>



<!--FIN PRODUCTO-->
@endsection


            