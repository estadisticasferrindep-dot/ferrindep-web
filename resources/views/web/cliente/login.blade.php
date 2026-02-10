@extends('layouts.plantilla')

@section('title', 'Mi Cuenta - Acceso')

@section('content')
    <div style="background-color: #000000; min-height: 80vh; padding-top: 60px; padding-bottom: 60px;">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-md-6 col-lg-5">
                    <div class="card shadow-lg border-0" style="background-color: #1a1a1a; border-radius: 12px;">
                        <div class="card-body p-5 text-center">
                            <h3 class="mb-3"
                                style="font-weight: 700; color: #ffffff; text-transform: uppercase; letter-spacing: 1px;">
                                Mi Cuenta</h3>
                            <p class="mb-4" style="color: #aaaaaa; font-size: 0.95rem;">
                                Ingrese su número de celular para consultar el estado de sus pedidos.
                            </p>

                            <form action="{{ route('web.mis_compras.buscar') }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <input type="tel" name="celular"
                                        class="form-control form-control-lg text-center text-white"
                                        placeholder="Ej: 1112345678" required
                                        style="border-radius: 50px; background-color: #333; border: 1px solid #444; color: #fff;">
                                </div>
                                <button type="submit" class="btn btn-primary w-100 btn-lg"
                                    style="border-radius: 50px; background-color: #F37021; border-color: #F37021; font-weight: 600;">
                                    CONSULTAR
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARRUSEL DE PRODUCTOS --}}
            @if(isset($productosCarousel) && count($productosCarousel) > 0)
                <div class="mt-5 pt-4 border-top border-secondary">
                    <h4 class="text-center text-white mb-4"
                        style="font-weight: 300; text-transform: uppercase; letter-spacing: 2px;">Algunos de nuestros productos
                    </h4>

                    <div id="carouselProductosLogin" class="carousel slide" data-bs-ride="carousel">
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
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselProductosLogin"
                            data-bs-slide="prev" style="width: 5%;">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselProductosLogin"
                            data-bs-slide="next" style="width: 5%;">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Siguiente</span>
                        </button>
                    </div>
                </div>
            @endif

        </div>
        </div>
    </div>

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