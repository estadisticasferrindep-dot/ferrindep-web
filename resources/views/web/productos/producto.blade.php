@extends('layouts.plantilla')

@section('title', $producto->medidas->medidas . ' ' . $producto->espesor->espesor)

@section('content')



  {{-- ===== ESTILOS CSS ===== --}}
  <script>
    console.log("🔍 PHP DIAGNOSTICS:");
    console.log("PHP SESSION LOCATION:", @json($ubicacionCliente ?? 'NULL'));
    console.log("PHP ZONA ID:", @json($zonaId ?? 'NULL'));
    console.log("SHIPPING RATES:", @json($shippingRates ?? []));
    console.log("REAL ZONA ID:", @json($realZonaId ?? 'NULL'));
  </script>
  <style>
    /* MOBILE */
    @media (max-width: 640px) {
      #product-hero {
        width: 100% !important;
      }

      #hero-box {
        position: relative !important;
        width: 100% !important;
      }

      #hero-box::before {
        content: "";
        display: block;
        padding-top: 100%;
      }

      #hero-box>img {
        position: absolute !important;
        inset: 0 !important;
        width: 100% !important;
        height: 100% !important;
        object-fit: contain !important;
        display: block !important;
        max-width: none !important;
      }

      .product-thumbs img {
        width: 88px !important;
        height: 88px !important;
        object-fit: cover !important;
        cursor: pointer;
      }
    }

    /* DESKTOP */
    @media (min-width: 641px) {
      #hero-box {
        position: static !important;
      }

      #hero-box::before {
        content: none !important;
      }

      #product-hero img#foto-principal {
        position: static !important;
        width: 100% !important;
        max-width: 100% !important;
        height: auto !important;
        object-fit: initial !important;
        display: block !important;
      }
    }

    .section-home-categorias #product-hero img#foto-principal {
      width: 100% !important;
      max-width: 100% !important;
      height: auto !important;
    }
  </style>

  <section class="section-equipo section-categoria" style="margin-top:87px;">
    <div class="container">
      <div class="row">

        {{-- SIDEBAR CATEGORÍAS --}}
        <section class="nav-categorias col-12 col-md-3 d-none d-md-flex">
          <div class="list-group list-group-flush">
            @foreach ($familias as $familia)
              <a href="{{route('web.productos.productos2', $familia->id)}}"
                style="color:#FD914D!important;padding-left:0;font-weight:400;display:flex;justify-content:space-between;"
                class="d-none d-md-flex cat-no-activa list-group-item list-group-item-action list-caracteristica">
                {{$familia->nombre}} <i class="fas fa-angle-down"></i>
              </a>
              <a href="{{route('web.productos.productos2.mobile', $familia->id)}}"
                style="color:#FD914D!important;padding-left:0;font-weight:400;display:flex;justify-content:space-between;"
                class="d-flex d-md-none cat-no-activa list-group-item list-group-item-action list-caracteristica">
                {{$familia->nombre}} <i class="fas fa-angle-down"></i>
              </a>
              @if ($familia->id == $familiaElegida)
                @foreach ($categorias as $item)
                  @if ($item->show && $item->tieneProductosFamilia($familia->id))
                    <a href="{{route('web.productos.categoria', [$item->id, $familia->id])}}"
                      class="d-none d-md-flex cat-no-activa list-group-item list-group-item-action list-caracteristica">
                      {{$item->con_nombre ? $item->nombre : (intval($item->nombre) >= 100 ? $item->nombre / 100 . ' m alto/ancho' : $item->nombre . ' cm alto/ancho')}}
                    </a>
                    <a href="{{route('web.productos.categoria.mobile', [$item->id, $familia->id])}}"
                      class="d-flex d-md-none cat-no-activa list-group-item list-group-item-action list-caracteristica">
                      {{$item->con_nombre ? $item->nombre : (intval($item->nombre) >= 100 ? $item->nombre / 100 . ' m alto/ancho' : $item->nombre . ' cm alto/ancho')}}
                    </a>
                  @endif
                  @foreach ($productos as $item2)
                    @if ($item2->show && $item->id == $item2->categoria_id && $item2->familia_id == $familiaElegida)
                      @if ($item2->con_nombre)
                        <a href="{{ route('web.productos.producto', $item2->id) }}#prod"
                          class="{{ $item2->id == $producto->id ? 'prod-activo ' : 'prod-no-activo '}} list-group-item d-none d-md-flex list-group-item-action list-trabajo"
                          style="padding-left:35px;">{{$item2->nombre}}</a>
                        <a href="{{ route('web.productos.producto.mobile', $item2->id) }}"
                          class="{{ $item2->id == $producto->id ? 'prod-activo ' : 'prod-no-activo '}} list-group-item d-flex d-md-none list-group-item-action list-trabajo"
                          style="padding-left:35px;">{{$item2->nombre}}</a>
                      @else
                        <a href="{{ route('web.productos.producto', $item2->id) }}#prod"
                          class="{{ $item2->id == $producto->id ? 'prod-activo ' : 'prod-no-activo '}} list-group-item d-none d-md-flex list-group-item-action list-trabajo"
                          style="padding-left:35px;">{{$item2->medidas->medidas}} {{$item2->espesor->espesor}}</a>
                        <a href="{{ route('web.productos.producto.mobile', $item2->id) }}"
                          class="{{ $item2->id == $producto->id ? 'prod-activo ' : 'prod-no-activo '}} list-group-item d-flex d-md-none list-group-item-action list-trabajo"
                          style="padding-left:35px;">{{$item2->medidas->medidas}} {{$item2->espesor->espesor}}</a>
                      @endif
                    @endif
                  @endforeach
                @endforeach
              @endif
            @endforeach
          </div>
        </section>

        {{-- CONTENIDO CENTRAL --}}
        <section class="section-home-categorias col-12 col-md-9" id="mobile">
          <div class="container">
            <div class="row">

              {{-- FOTOS (Universal para todos los productos) --}}
              <div class="col-12 col-md-5" style="margin-bottom:70px;">
                @php
                  use Illuminate\Support\Facades\Storage;
                  $PLH = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
                  $principalUrl = !empty($producto->imagen) ? asset(Storage::url($producto->imagen)) : null;
                  $galeriaUrls = collect($producto->galerias ?? [])->filter(fn($g) => !empty($g->imagen))->map(fn($g) => asset(Storage::url($g->imagen)))->values();
                  if (!$principalUrl) {
                    $principalUrl = $galeriaUrls->first() ?: $PLH;
                  }
                @endphp

                <div id="product-hero" class="product-hero">
                  <div id="hero-box" class="ratio-box">
                    <img id="foto-principal" src="{{ $principalUrl }}" alt="{{ $producto->nombre }}" loading="lazy"
                      style="width:100% !important; max-width:100% !important; height:auto !important; display:block !important;">
                  </div>
                  @if (!$producto->hay_stock)
                    <div class="ribbon ribbon-top-left sin_stock"><span style="background-color:#6c757d;">SIN STOCK</span>
                    </div>
                  @elseif ($producto->oferta)
                    <div class="ribbon ribbon-top-left"><span>¡OFERTA!</span></div>
                  @endif

                  {{-- Mobile Image Counter --}}
                  <div id="mobile-img-counter"
                    style="position:absolute; bottom:10px; right:10px; background:rgba(0,0,0,0.6); color:white; padding:4px 10px; border-radius:15px; font-size:12px; font-weight:bold; display:none; z-index:10; pointer-events:none;">
                    1 / 1
                  </div>
                </div>

                @if($galeriaUrls->count())
                  <div class="d-flex flex-wrap gap-2 mt-3 product-thumbs">
                    @foreach($galeriaUrls as $url)
                      <img src="{{ $url }}" alt="Vista" class="img-thumbnail fm-thumb" loading="lazy"
                        onclick="document.getElementById('foto-principal').src=this.src;">
                    @endforeach
                  </div>
                @endif
              </div>

              {{-- COLUMNA DERECHA (Info + Compra) --}}
              <div class="col-12 col-md-7">

                <div>
                  <div class="tabla-trabajo"
                    style="font: normal normal bold 14px/19px Open Sans; letter-spacing:0; color:#939292;">
                    @if($producto->categoria->con_nombre == 1) {{$producto->categoria->nombre}} @else Rollos
                    {{$producto->categoria->nombre}}cm de ancho @endif
                  </div>
                  @if ($producto->con_nombre)
                    <div class="tabla-trabajo" style="font: normal normal bold 28px/38px Open Sans;">{{$producto->nombre}}
                    </div>
                  @else
                    <div class="tabla-trabajo" style="font: normal normal bold 28px/38px Open Sans;">
                      {{$producto->medidas->medidas}} {{$producto->espesor->espesor}}
                    </div>
                  @endif

                  @if(config('cart.session_enabled') && in_array($producto->id, config('cart.products')))
                    {{-- SESSION CART UI --}}
                    <div class="mt-4 p-3 bg-light border rounded">
                      <h5 class="mb-3">Seleccione Presentación</h5>
                      <table class="table table-borderless table-sm mb-0">
                        <thead>
                          <tr>
                            <th>Variante</th>
                            <th>Precio</th>
                            <th style="width:120px;" class="text-center">Cantidad</th>
                          </tr>
                        </thead>
                        <tbody>
                          @php
                            $sessionCartItems = \Illuminate\Support\Facades\Session::get('cart_session_items', []);
                          @endphp
                          @foreach($producto->presentaciones as $pres)
                            @php
                              $qty = $sessionCartItems[$pres->id]['qty'] ?? 0;
                            @endphp
                            <tr>
                              <td class="align-middle">{{ $pres->medida ?? $pres->metros }}m</td>
                              <td class="align-middle">${{ number_format($pres->precio, 0, ',', '.') }}
                                {{-- shipping cost logic --}}
                                @if(!$producto->anular_envio && isset($zonaId) && $zonaId && isset($shippingRates[0]['costo']))
                                  @php
                                    $isFree = false;
                                    $realZ = $realZonaId ?? $zonaId;

                                    if ($realZ == 1 && $pres->envio_gratis_zona_1)
                                      $isFree = true;
                                    elseif ($realZ == 2 && $pres->envio_gratis_zona_2)
                                      $isFree = true;
                                    elseif ($realZ == 3 && $pres->envio_gratis_zona_3)
                                      $isFree = true;
                                    elseif ($realZ == 4 && $pres->envio_gratis_zona_4)
                                      $isFree = true;

                                    // Logica Split Weight Simplificada para Display
                                    $finalCost = 0;
                                    if (!$isFree) {
                                      $baseCost = $shippingRates[0]['costo'];
                                      $pesoUnit = $pres->peso ?? 0;
                                      $bultos = max(1, ceil($pesoUnit / 30));
                                      $finalCost = $baseCost * $bultos;
                                    }
                                  @endphp

                                  <div style="font-size: 11px; margin-top: 4px; line-height: 1.2;">
                                    @if($isFree)
                                      <span style="color: #28a745; font-weight: bold;">
                                        <i class="fas fa-truck" style="font-size: 10px;"></i> Envío sin cargo a
                                        {{ $locationName }}
                                      </span>
                                    @else
                                      <span style="color: #666;">
                                        <i class="fas fa-truck" style="font-size: 10px;"></i> Entrega a {{ $locationName }}
                                        ${{ number_format($finalCost, 0, ',', '.') }}
                                      </span>
                                    @endif
                                  </div>
                                @endif
                              </td>
                              <td class="align-middle">
                                <div class="qty-controls d-flex align-items-center justify-content-center gap-1"
                                  data-id="{{ $pres->id }}">
                                  <button class="btn btn-outline-secondary btn-sm"
                                    onclick="CartSession.changeQty({{ $pres->id }}, -1)" type="button"
                                    style="width:28px">-</button>
                                  <input type="text" class="form-control form-control-sm text-center qty-input"
                                    data-id="{{ $pres->id }}" value="{{ $qty }}" style="width: 40px;" readonly>
                                  <button class="btn btn-outline-secondary btn-sm"
                                    onclick="CartSession.changeQty({{ $pres->id }}, 1)" type="button"
                                    style="width:28px">+</button>
                                </div>
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                      <div class="mt-3 text-end">
                        <a href="{{ route('web.carrito') }}" class="btn btn-primary text-uppercase fw-bold"
                          style="background:#FD914D; border:none; font-size:14px;">Ir al Carrito <i
                            class="fas fa-shopping-cart ms-1"></i></a>
                      </div>
                    </div>
                    <script src="{{ asset('js/cart-session.js') }}"></script>
                  @else
                    {{-- VUE LEGACY CART --}}
                    <add-to-cart descripcion="{{ $producto->descripcion }}" medidas="{{ $producto->medidas->medidas }}"
                      espesor="{{ $producto->espesor->espesor }}" ancho="{{$producto->categoria->nombre}}"
                      familia="{{$producto->familia->nombre}}" con-nombre="{{$producto->con_nombre}}"
                      vendidos="{{$producto->vendidos}}" anula-envio="{{$producto->anular_envio}}"
                      presentaciones="{{ $producto->presentaciones }}" imagen="{{ $principalUrl }}" id="{{ $producto->id }}"
                      oferta="{{$producto->oferta}}" ruta="{{route('web.carrito', [], false)}}" ref="me"
                      :shipping-rates="{{ json_encode($shippingRates ?? []) }}" :zona-id="{{ $zonaId ?? 'null' }}"
                      :real-zona-id="{{ $realZonaId ?? $zonaId ?? 'null' }}" location-name="{{ $locationName ?? '' }}"
                      location-full-name="{{ $ubicacionCliente->cityName ?? $ubicacionCliente->regionName ?? '' }}"
                      :location-partido="{{ json_encode($ubicacionCliente->partido ?? '') }}" :zonas="{{ $zonas }}"
                      :destinos="{{ $destinos }}" :destino-zonas="{{ $destinozonas }}" />
                  @endif
                </div>

                {{-- DETECTED SHIPPING COST REMOVED (Moved to Vue) --}}
                <div id="shipping-estimator-placeholder"></div>

                <!-- Removed Static Block -->

              </div>
            </div>

            <div class="row">
              <div class="col-12">
                <h4 style="font: normal normal medium 16px/21px Rubik; letter-spacing:0; color:#505050;">Características
                  técnicas</h4>
                <div class="caracteristicas">{!! $producto->caracteristicas !!}</div>
              </div>
            </div>
            @if($producto->video)
              <div class="row my-5">
                <div class="col-12"><iframe class="video-prod" width="100%" height="500px"
                    src="https://www.youtube.com/embed/{{$producto->video}}?loop=1&autoplay=1&playlist={{$producto->video}}"
                    title="YouTube video player" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe></div>
              </div>
            @endif
            <div class="row">
              <div class="col-12">
                <h4 style="font: normal normal medium 16px/21px Rubik; letter-spacing:0; color:#505050;">Usos</h4>
                <div class="usos">{!! $producto->usos !!}</div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </section>
@endsection

@section('scripts')
  @parent

  <style>
    @media (max-width: 640px) {
      #hero-box {
        position: static !important;
      }

      #hero-box::before {
        content: none !important;
        display: none !important;
      }

      #hero-box>img,
      #product-hero img#foto-principal {
        position: static !important;
        width: 100% !important;
        max-width: 100% !important;
        height: auto !important;
        max-height: none !important;
        object-fit: contain !important;
        display: block !important;
      }
    }
  </style>

  <script>   
   window.colores = @json($colores ?? []); 
   
   // Construct Swipe Gallery Images
   window.productImages = [];
   @if(!empty($principalUrl))
     window.productImages.push("{{ $principalUrl }}");
   @endif
   
   var galleryUrls = @json(($producto->galerias ?? collect())->map(function ($g) {
     return $g->imagen ? asset(\Illuminate\Support\Facades\Storage::url($g->imagen)) : null;
   })->filter()->values());
   
   window.productImages = window.productImages.concat(galleryUrls);
   // Remove duplicates
   window.productImages = [...new Set(window.productImages)];

   document.addEventListener('DOMContentLoaded', function() {
       // Only active if we have images and on mobile (check width)
       if (window.productImages.length > 1 && window.innerWidth <= 640) {
           const counter = document.getElementById('mobile-img-counter');
           const heroBox = document.getElementById('hero-box'); // Container
           
           // Ensure container clips overflow for sliding
           heroBox.style.overflow = 'hidden'; 
           
           if(counter) {
               counter.style.display = 'block';
               counter.textContent = `1 / ${window.productImages.length}`;
           }

           let currentIndex = 0;
           let touchStartX = 0;
           let touchEndX = 0;
           let isAnimating = false;

           heroBox.addEventListener('touchstart', e => {
               if(isAnimating) return;
               touchStartX = e.changedTouches[0].screenX;
           }, {passive: true});

           heroBox.addEventListener('touchend', e => {
               if(isAnimating) return;
               touchEndX = e.changedTouches[0].screenX;
               handleSwipe();
           }, {passive: true});

           function handleSwipe() {
               const threshold = 40;
               if (touchEndX < touchStartX - threshold) {
                   // Swipe Left -> Next
                   nextImage();
               }
               if (touchEndX > touchStartX + threshold) {
                   // Swipe Right -> Prev
                   prevImage();
               }
           }

           function nextImage() {
               let nextIndex = (currentIndex + 1) % window.productImages.length;
               animateSlide(nextIndex, 'next');
               currentIndex = nextIndex;
           }

           function prevImage() {
               let prevIndex = (currentIndex - 1 + window.productImages.length) % window.productImages.length;
               animateSlide(prevIndex, 'prev');
               currentIndex = prevIndex;
           }

           function animateSlide(targetIndex, direction) {
               isAnimating = true;
               
               const currentImg = document.getElementById('foto-principal');
               const newUrl = window.productImages[targetIndex];
               
               // Create new Image
               const newImg = document.createElement('img');
               newImg.src = newUrl;
               newImg.className = currentImg.className; // Copy classes? typically none specific
               newImg.id = 'temp-slide-img';
               
               // Apply critical styles from CSS (since they use !important in CSS, distinct inline styles might be needed or just rely on CSS)
               // The CSS targets #hero-box > img. Both will match.
               // We need to override transform placement manually.
               
               // Initial Position
               const startTranslate = direction === 'next' ? '100%' : '-100%';
               newImg.style.transform = `translateX(${startTranslate})`;
               // Ensure z-index is top?
               newImg.style.zIndex = '2';
               
               heroBox.appendChild(newImg);
               
               // Force Reflow
               void newImg.offsetWidth;
               
               // Prepare Transitions
               const duration = 0.3; // seconds
               currentImg.style.transition = `transform ${duration}s ease-in-out`;
               newImg.style.transition = `transform ${duration}s ease-in-out`;
               
               // Execute Slide
               // Current moves opposite
               const exitTranslate = direction === 'next' ? '-100%' : '100%';
               currentImg.style.transform = `translateX(${exitTranslate})`;
               newImg.style.transform = `translateX(0)`;
               
               // Cleanup
               setTimeout(() => {
                   // Update Real Src
                   currentImg.style.display = 'none'; // Hide briefly to avoid flicker reset
                   currentImg.src = newUrl;
                   
                   // Reset Styles on Current
                   currentImg.style.transition = '';
                   currentImg.style.transform = '';
                   currentImg.style.display = ''; // Restore
                   
                   // Remove Temp
                   newImg.remove();
                   
                   // Update Counter
                   if(counter) {
                       counter.textContent = `${targetIndex + 1} / ${window.productImages.length}`;
                   }
                   
                   isAnimating = false;
               }, duration * 1000 + 10);
           }
       }
   });
  </script>

  <script>   (function () { var img = document.getElementById('foto-principal'); function fixHeroSize() { if (!img) return; img.removeAttribute('width'); img.removeAttribute('height'); img.style.width = '100%'; img.style.maxWidth = '100%'; img.style.height = 'auto'; img.style.display = 'block'; } function setHeroRatio() { var box = document.getElementById('hero-box'); if (!img || !box) return; var nw = img.naturalWidth || img.width, nh = img.naturalHeight || img.height; if (nw > 0 && nh > 0) { box.style.setProperty('--pad', (nh / nw * 100) + '%'); } } ['load', 'resize'].forEach(function (ev) { window.addEventListener(ev, function () { fixHeroSize(); setHeroRatio(); }); }); document.addEventListener('DOMContentLoaded', function () { fixHeroSize(); setHeroRatio(); if (img) { img.addEventListener('load', function () { fixHeroSize(); setHeroRatio(); }); } setTimeout(function () { fixHeroSize(); setHeroRatio(); }, 1200); }); })();
  </script>

  {{-- SCRIPT CORTADOR (SOLO PARA PRODUCTO 1) --}}
  {{-- SCRIPT CORTADOR REMOVED --}}
@endsection