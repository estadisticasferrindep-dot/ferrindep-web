@extends('layouts.plantilla')

@section('title', $title)

@section('content')
    <div style="background-color: #000000; min-height: 80vh; padding-top: 40px; padding-bottom: 60px; color: #ffffff;">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center mb-5">
                <h2 style="font-weight: 700; margin: 0;">
                    Hola, {{ $nombreCliente }}...
                </h2>
                <a href="{{ route('web.mis_compras') }}" class="btn btn-outline-light btn-sm">
                    Salir
                </a>
            </div>

            @if(isset($error))
                <div class="alert alert-danger">
                    {{ $error }}
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('web.mis_compras') }}" class="btn btn-primary">Volver a intentar</a>
                </div>
            @else

                <div class="row">
                    <div class="col-12">
                        @forelse($pedidos as $pedido)
                            <div class="card mb-4 border-0 shadow-lg" style="background-color: #1a1a1a; border-radius: 8px;">
                                <div class="card-body p-4">
                                    <div class="row align-items-center">
                                        {{-- COLUMNA 1: Info General --}}
                                        <div class="col-md-3 border-end border-secondary mb-3 mb-md-0">
                                            <div class="mb-2">
                                                <span
                                                    class="badge bg-warning text-dark">{{ $pedido->created_at->format('d/m/Y') }}</span>
                                                @if($pedido->estado_personalizado)
                                                    <span class="badge"
                                                        style="background-color: #F37021;">{{ $pedido->estado_personalizado }}</span>
                                                @else
                                                    <span class="badge bg-secondary ms-1">Pendiente</span>
                                                @endif
                                            </div>
                                            <h4 class="fw-bold text-white mb-1">Pedido #{{ $pedido->id }}</h4>
                                            <div class="text-success fw-bold fs-4">${{ number_format($pedido->total, 0, ',', '.') }}
                                            </div>
                                        </div>

                                        {{-- COLUMNA 2: Items --}}
                                        <div class="col-md-6 border-end border-secondary mb-3 mb-md-0">
                                            {{-- <h6 class="text-muted text-uppercase small mb-3">Productos</h6> --}}
                                            <ul class="list-unstyled mb-0 w-100">
                                                @foreach($pedido->itemsPedidos as $item)
                                                    <li class="d-flex align-items-center mb-2 pb-2 border-bottom border-dark">

                                                        {{-- IMAGEN PROD --}}
                                                        <div
                                                            style="flex: 0 0 auto; width: 50px; height: 50px; background-color: #fff; margin-right: 15px; border-radius: 4px; overflow: hidden; display:flex; align-items:center; justify-content:center;">
                                                            @if(optional($item->producto)->imagen)
                                                                <img src="{{ asset(Storage::url($item->producto->imagen)) }}" alt="img"
                                                                    style="max-width: 100%; max-height: 100%;">
                                                            @else
                                                                <span style="color: #ccc; font-size: 10px;">Sin foto</span>
                                                            @endif
                                                        </div>

                                                        <div class="flex-grow-1" style="min-width: 0;">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <div>
                                                                    <span class="fw-bold d-block text-white">
                                                                        {{ $item->familia }}
                                                                        {{ $item->nombre ?: (optional($item->producto)->nombre ?? '') }}
                                                                    </span>
                                                                    <small class="text-muted d-block" style="font-size: 0.8rem;">
                                                                        {{ $item->medidas }}
                                                                        {{ $item->ancho ? '| ' . $item->ancho . 'cm' : '' }}
                                                                        {{ $item->espesor ? '| ' . $item->espesor : '' }}
                                                                        {{ $item->metros ? '(' . $item->metros . 'm)' : '' }}
                                                                    </small>
                                                                </div>
                                                                <span class="badge bg-dark border border-secondary ms-2"
                                                                    style="white-space:nowrap;">{{ $item->cantidad }}
                                                                    {{ $item->cantidad == 1 ? 'rollo' : 'rollos' }}</span>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                        {{-- COLUMNA 3: Entrega --}}
                                        <div class="col-md-3">
                                            <h6 class="text-muted text-uppercase small mb-2">Forma de entrega:</h6>
                                            <div class="d-flex align-items-center text-white">
                                                @if(strtolower($pedido->envio) == 'fabrica')
                                                    <i class="fas fa-warehouse fa-lg me-2 text-warning"></i>
                                                    <span class="fw-bold">Retiro en Depósito</span>
                                                @else
                                                    <i class="fas fa-truck fa-lg me-2 text-warning"></i>
                                                    <span class="fw-bold">Envío a Domicilio</span>
                                                @endif
                                            </div>
                                            @if(strtolower($pedido->envio) != 'fabrica')
                                                <div class="small text-muted mt-1 ps-4">
                                                    {{ $pedido->localidad_envio ?? $pedido->descCaba }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <h4 class="text-muted">No se encontraron pedidos con el celular: {{ $celular }}</h4>
                                <a href="{{ route('web.mis_compras') }}" class="btn btn-outline-light mt-3">Intentar con otro
                                    número</a>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- CARRUSEL DE PRODUCTOS SUGERIDOS --}}
                @if(isset($productosCarousel) && count($productosCarousel) > 0)
                    <div class="mt-5 pt-5 border-top border-secondary">
                        <h4 class="text-center text-white mb-4"
                            style="font-weight: 300; text-transform: uppercase; letter-spacing: 2px;">Algunos de nuestros productos
                        </h4>

                        <div id="carouselProductosHistorial" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @foreach($productosCarousel->chunk(4) as $key => $chunk)
                                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                        <div class="row justify-content-center">
                                            @foreach($chunk as $prod)
                                                <div class="col-6 col-md-3 mb-3">
                                                    <a href="{{ route('web.productos.producto', $prod) }}" style="text-decoration: none;">
                                                        <div class="card h-100 border-0" style="background-color: #1a1a1a;">
                                                            <div class="carousel-prod-container">
                                                                <img src="{{ asset(Storage::url($prod->imagen)) }}" class="img-fluid carousel-prod-img"
                                                                     alt="{{ $prod->nombre }}">
                                                            </div>
                                                            <div class="card-body text-center p-3">
                                                                <h6 class="card-title text-white text-truncate" style="font-size: 0.9rem;">
                                                                    {{ $prod->nombre ?: (optional($prod->medidas)->medidas . ' ' . optional($prod->espesor)->espesor) }}
                                                                </h6>

                                                                <div class="small text-muted mb-1" style="font-size: 0.75rem;">
                                                                    @if($prod->nombre)
                                                                        {{ optional($prod->medidas)->medidas }} {{ optional($prod->espesor)->espesor }}
                                                                    @endif
                                                                    
                                                                    @if(optional($prod->categoria)->nombre)
                                                                        @if($prod->nombre) | @endif
                                                                        {{ optional($prod->categoria)->nombre }}{{ is_numeric(optional($prod->categoria)->nombre) ? 'cm de ancho' : '' }}
                                                                    @endif
                                                                </div>

                                                                <span class="badge bg-secondary badge-wrap-mobile" style="font-size: 0.7rem;">
                                                                    {{ $prod->familia->nombre }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselProductosHistorial"
                                data-bs-slide="prev" style="width: 5%;">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Anterior</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselProductosHistorial"
                                data-bs-slide="next" style="width: 5%;">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Siguiente</span>
                            </button>
                        </div>
                    </div>
                @endif

            @endif
        </div>
        </div>
        

    </div>
@endsection

@section('scripts')
<style>
    /* Ocultar footer solo en esta vista por pedido del cliente */
    footer, .footer, #footer {
        display: none !important;
        visibility: hidden !important;
        height: 0 !important;
        overflow: hidden !important;
    }

    /* Estilos para el carrusel de productos */
    .carousel-prod-container {
        height: 200px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #fff;
    }
    
    .carousel-prod-img {
        max-height: 100%;
        width: auto;
    }

    @media (max-width: 768px) {
        .carousel-prod-container {
            height: 160px !important; /* Altura un poco menor en mobile para que no quede tanto aire */
        }
        .carousel-prod-img {
            width: 100% !important;
            height: 100% !important;
            object-fit: contain !important; /* Forzar que ocupe todo el espacio posible sin recortar */
            padding: 5px; /* Un mini padding para que no toque los bordes si es muy cuadrado */
            max-height: none !important;
        }

        /* Fix para que el texto de la familia (badge) baje de línea si es largo */
        .badge-wrap-mobile {
            white-space: normal !important;
            display: inline-block;
            line-height: 1.2;
            text-align: center;
        }
    }
</style>
@endsection
