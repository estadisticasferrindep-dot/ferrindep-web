@extends('layouts.plantilla')

@section('title', 'Ferrindep')

@section('content')

  <div class="video-header-container"
    style="position: relative; width: 100%; background-color: #000; overflow: hidden; display: block; margin:0; padding:0;">
    {{--
    TIP: Para el video final, reemplaza 'ruta/al/video.mp4' con la ubicación real del archivo.
    Por ahora, está configurado para un video en loop fijo.
    --}}
    <video id="header-video" autoplay muted playsinline
      style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; border:none; outline:none; z-index: 1;">
      {{-- Source injected by JS to prevent double download --}}
    </video>
  </div>
  <div class="data-home d-none d-md-flex">
    <div class="container">
      <div class="row">
        <div class="col-4" style="display:flex">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12 col-lg-4 mt-4 mt-md-0">
                <img src="{{asset(Storage::url($home->seccion_foto1))}}">
              </div>
              <div class="col-12 col-lg-8 d-md-flex flex-wrap text-center text-md-left mt-2 mt-lg-0 home-mobile">
                <h3>{{$home->seccion_titulo1}}</h3>
                <h5>{{$home->seccion_texto1}}</h5>
              </div>
            </div>
          </div>
        </div>

        <div class="col-4" style="display:flex">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12 col-lg-4 mt-4 mt-md-0 home-mobile">
                <img src="{{asset(Storage::url($home->seccion_foto2))}}">
              </div>
              <div class="col-12 col-lg-8 d-md-flex flex-wrap text-center text-md-left mt-2 mt-lg-0 home-mobile">
                <h3>{{$home->seccion_titulo2}}</h3>
                <h5>{{$home->seccion_texto2}}</h5>
              </div>
            </div>
          </div>
        </div>

        <div class="col-4" style="display:flex">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12 col-lg-4 mt-4 mt-md-0 home-mobile">
                <img src="{{asset(Storage::url($home->seccion_foto3))}}">
              </div>
              <div class="col-12 col-lg-8 d-md-flex flex-wrap text-center text-md-left mt-2 mt-lg-0 home-mobile"
                style="padding:0;">
                <h3>{{$home->seccion_titulo3}}</h3>
                <h5>{{$home->seccion_texto3}}</h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container" style="margin: 20px auto 15px;">
    <div class="card p-3 p-md-4 shadow-sm" style="border: none; background-color: #f8f9fa;">

      <h5 class="text-center" onclick="toggleBuscadorMobile()"
        style="margin-bottom: 20px; font-weight: bold; color: #444; font-size: 1.2rem; font-family: 'Open Sans', sans-serif; cursor: pointer;">
        Buscador r&aacute;pido <i class="fas fa-chevron-down" id="icon-chevron"></i>
      </h5>

      <div class="buscador-collapsible d-none d-md-block">
      <div class="d-flex justify-content-center mb-3 mb-md-4">
        <div class="btn-group" role="group" style="width: 100%; max-width: 500px;">

          <button type="button" class="btn btn-outline-primary active px-1 px-md-4 py-1 btn-familia" data-familia-id="1"
            style="font-weight: 600; border-radius: 20px 0 0 20px; font-size: 13px; line-height: 1.2; padding-top:4px !important; padding-bottom:4px !important;">
            <span class="d-none d-md-inline" style="font-size: 16px;">Mallas Electrosoldadas</span>
            <span class="d-inline d-md-none">Mallas<br>Electrosoldadas</span>
          </button>

          <button type="button" class="btn btn-outline-primary px-1 px-md-4 py-1 btn-familia" data-familia-id="2"
            style="font-weight: 600; border-radius: 0 20px 20px 0; font-size: 13px; line-height: 1.2; padding-top:4px !important; padding-bottom:4px !important;">
            <span class="d-none d-md-inline" style="font-size: 16px;">Metal Desplegado</span>
            <span class="d-inline d-md-none">Metal<br>Desplegado</span>
          </button>

        </div>
      </div>

    </div>
    <div class="buscador-collapsible d-none d-md-block">
    <h4 id="titulo-familia" class="text-center mt-2 mb-3"
      style="font-weight: 700; color: #0d6efd; font-family: 'Open Sans', sans-serif;">
      Mallas Electrosoldadas
    </h4>


    <form id="form-filtros">
      <div class="row g-2 g-md-3">

        <div class="col-6 col-md-3">
          <label class="form-label text-muted font-weight-bold small mb-1">ANCHO DEL ROLLO</label>
          <select id="filtro-ancho" class="form-select form-select-sm filtro-select">
            <option value="" data-familia="all">Seleccionar</option>
            @foreach($anchos as $ancho)
              @php
                $val = intval($ancho->nombre);
                $texto = ($val >= 100) ? ($val / 100) . ' m' : $val . ' cm';
              @endphp
              <option value="{{ $ancho->id }}" data-familia="{{ $ancho->familia_id }}">{{ $texto }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-6 col-md-3">
          <label class="form-label text-muted font-weight-bold small mb-1">MEDIDA</label>
          <select id="filtro-medida" class="form-select form-select-sm filtro-select">
            <option value="" data-familia="all">Seleccionar</option>
            @foreach($medidas as $medida)
              <option value="{{ $medida->id }}" data-familia="{{ $medida->familia_id }}">{{ $medida->medidas }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-6 col-md-3">
          <label id="label-espesor" class="form-label text-muted font-weight-bold small mb-1">ESPESOR (Alambre)</label>
          <select id="filtro-espesor" class="form-select form-select-sm filtro-select">
            <option value="" data-familia="all">Seleccionar</option>
            @foreach($espesores as $espesor)
              <option value="{{ $espesor->id }}" data-familia="{{ $espesor->familia_id }}">{{ $espesor->espesor }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-6 col-md-3 d-flex align-items-end">
          <button type="button" id="btn-limpiar-filtros" class="btn btn-outline-secondary btn-sm w-100">
            Limpiar
          </button>
        </div>

      </div>
    </form>
    </div>
  </div>

  <div id="msg-sin-resultados" class="alert alert-warning mt-3 d-none text-center py-2 small">
    No encontramos productos con esa combinaci&oacute;n en la familia seleccionada.
  </div>
  </div>
  <section class="section-home-categorias section-categoria" style="margin-bottom:58px;">
    <div class="container container-productos" style="margin-top:60px;">
      <div id="grid-productos" class="row">

        @php
          $mlGo = [1 => '/go/ml/10x10-30', 40 => '/go/ml/15x15-30', 54 => '/go/ml/25x25-30'];
          $mlFallback = '/go/ml/tienda';
          $destacarTodos = true;
        @endphp

        @php $previousWidth = null; @endphp
        @foreach ($productos as $item)
          @if ($item->destacado && $item->show)

            @php
              $currentWidth = $item->categoria->con_nombre ? $item->categoria->nombre : (intval($item->categoria->nombre) >= 100 ? $item->categoria->nombre / 100 . ' m ancho' : $item->categoria->nombre . ' cm ancho');
            @endphp

            @if ($currentWidth != $previousWidth)
              <div class="header-mobile-categoria d-flex d-md-none col-12 align-items-center"
                style="background:#2c3e50; margin-top:5px; margin-bottom:5px; padding:4px 15px; font-weight:700; color:#fff; border-radius:6px; gap:8px; font-size:1rem;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                  stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                  <path d="M4 12h16m0 0l-4-4m4 4l-4 4M4 12l4-4m-4 4l4 4" />
                </svg>
                <span>Rollos {{ $currentWidth }}</span>
              </div>
              @php $previousWidth = $currentWidth; @endphp
            @endif

            <div class="card-buscable d-none d-md-flex col-md-3" style="margin-bottom:40px;"
              data-familia="{{ $item->familia_id }}" data-ancho="{{ $item->categoria_id }}"
              data-medida="{{ $item->medida_id }}" data-espesor="{{ $item->espesor_id }}">

              <div style="border:1px solid rgba(143,134,110,.3); padding:8px; width:100%; height:100%;">
                <a href="{{ route('web.productos.producto', $item) }}" style="text-decoration:none">
                  <div class="img-border-categorias img-active"
                    style="position: relative; overflow: hidden; background: #fff;">
                    <img src="{{asset(Storage::url($item->imagen))}}" alt="{{ $item->nombre }}" loading="lazy"
                      style="width: 100%; height: 100%; object-fit: contain; position: absolute; top: 0; left: 0;">
                    @if($item->oferta)
                      <div class="oferta">OFERTA</div>
                    @endif
                  </div>
                  <hr>
                  <div class="text-box-categorias">
                    <div class="tabla-trabajo" style="font:normal 11px/16px Open Sans;color:#000;margin-top:0;">
                      <span style="font-size:120%">{{ $item->familia->nombre }}</span>
                    </div>

                    @if ($item->con_nombre)
                      <div class="tabla-trabajo" style="font: normal normal bold 18px/28px Open Sans; color: black;">
                        {{$item->nombre}}
                      </div>
                    @else
                      <div class="tabla-trabajo" style="font: normal normal bold 18px/28px Open Sans; color: black;">
                        {{$item->medidas->medidas}} {{$item->espesor->espesor}}
                      </div>
                    @endif

                    <div class="tabla-trabajo"
                      style="font: normal normal bold 12px/17px Open Sans; color:#939292; margin-top:0;">
                      <span style="font-size:120%">
                        {{$item->categoria->con_nombre ? $item->categoria->nombre : (intval($item->categoria->nombre) >= 100 ? $item->categoria->nombre / 100 . ' m alto/ancho' : $item->categoria->nombre . ' cm alto/ancho') }}
                      </span>
                    </div>

                    <hr>
                    <desde-categorias desc-efectivo="{{$configuracionPedidos->descuento_efectivo}}"
                      desc-transferencia="{{$configuracionPedidos->descuento_transferencia}}"
                      desc-mp="{{$configuracionPedidos->descuento_mp}}" vendidos="{{$item->vendidos}}"
                      presentaciones="{{ $item->presentaciones }}" oferta="{{$item->oferta}}"
                      con-nombre="{{$item->con_nombre}}" />
                  </div>
                </a>
              </div>
            </div>

            @php
              // Prepare gallery URLs for JS
              $galleryUrls = $item->galerias->map(function($g) {
                  return asset(Storage::url($g->imagen));
              })->toArray();
              // Add main image as first element if not empty
              if($item->imagen) {
                  array_unshift($galleryUrls, asset(Storage::url($item->imagen)));
              }
              // Only needed if we have > 1 image
              $hasGallery = count($galleryUrls) > 1;
              $galleryJson = $hasGallery ? json_encode($galleryUrls) : '';
            @endphp

            <div class="card-buscable d-flex d-md-none col-12" style="margin-bottom:12px; padding:0;"
              data-familia="{{ $item->familia_id }}" data-ancho="{{ $item->categoria_id }}"
              data-medida="{{ $item->medida_id }}" data-espesor="{{ $item->espesor_id }}">

              <div class="product-card-mobile" style="background:#fff; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,0.1); border:1px solid #f0f0f0; width:100%; overflow:hidden; position:relative;"
                   @if($hasGallery) data-gallery='{{ $galleryJson }}' @endif>
                
                {{-- Main Clickable Area --}}
                <a href="{{ route('web.productos.producto', $item) }}" style="text-decoration:none; color:inherit; display:block;">
                  <div class="row g-0">
                    {{-- Image Col --}}
                    <div class="col-4" style="background:#fff; min-height:110px; position:relative;">
                       <div class="img-border-categorias img-active lazy-bg mobile-slideshow-img"
                        data-bg="url({{asset(Storage::url($item->imagen))}})"
                        style="width:100%; height:100%; background-size:contain; background-position:center; background-repeat:no-repeat; transition: background-image 0.3s ease-in-out;">
                        @if($item->oferta)
                          <div class="oferta" style="font-size:10px; padding:2px 6px;">OFERTA</div>
                        @endif
                      </div>
                    </div>

                    {{-- Text Col --}}
                    <div class="col-8" style="padding:10px 12px 10px 4px; display:flex; flex-direction:column; justify-content:center;">
                      
                      {{-- Title --}}
                      <div style="font-family:'Open Sans', sans-serif; font-size:11px; color:#666; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:2px;">
                        {{ $item->familia->nombre }}
                      </div>

                      <div style="font-family:'Open Sans', sans-serif; font-size:15px; font-weight:800; color:#222; line-height:1.3; margin-bottom:4px;">
                        @if ($item->con_nombre)
                          {{$item->nombre}}
                        @else
                          {{$item->medidas->medidas}} <span style="font-weight:400;">{{$item->espesor->espesor}}</span>
                        @endif
                      </div>

                       <div style="font-size:13px; color:#555; font-weight:500; margin-bottom:8px;">
                        {{$item->categoria->con_nombre ? $item->categoria->nombre : (intval($item->categoria->nombre) >= 100 ? $item->categoria->nombre / 100 . ' m ancho' : $item->categoria->nombre . ' cm ancho') }}
                      </div>

                      @php
                          $maxPricePerMeter = 0;
                          if($item->presentaciones->isNotEmpty()) {
                              foreach($item->presentaciones as $pres) {
                                  $metros = floatval($pres->metros); 
                                  $precio = floatval($pres->precio);
                                  if($metros > 0 && $precio > 0) {
                                      $pMm = $precio / $metros;
                                      if($pMm > $maxPricePerMeter) {
                                          $maxPricePerMeter = $pMm;
                                      }
                                  }
                              }
                          }
                      @endphp
                      
                      @if($maxPricePerMeter > 0)
                          <div style="font-size: 13px; color: #FD914D; font-weight: 700; margin-bottom: 6px;">
                              Desde ${{ number_format($maxPricePerMeter, 0, ',', '.') }} por metro
                          </div>
                      @endif

                      {{-- Price & CTA --}}
                      <div class="d-flex align-items-center justify-content-between">
                         <span style="color:#FD914D; font-weight:800; font-size:12px; letter-spacing:0.5px;">
                           VER DETALLE <i class="fas fa-chevron-right" style="font-size:10px;"></i>
                         </span>
                      </div>

                    </div>
                  </div>
                </a>

                {{-- Subtle ML Link (Bottom Right) --}}
                @php
                  $mlHref = $mlGo[$item->id] ?? $mlFallback;
                @endphp
                <div style="position:absolute; bottom:6px; right:10px;">
                  <a href="{{ $mlHref }}" target="_blank" rel="noopener" 
                     style="font-size:10px; color:#999; text-decoration:none; display:flex; align-items:center; gap:3px;">
                     O ver en MercadoLibre <i class="fas fa-external-link-alt" style="font-size:9px;"></i>
                  </a>
                </div>

              </div>
            </div>

<!-- Smart Slideshow JS -->
@once
<script>
document.addEventListener('DOMContentLoaded', function() {
    if ('IntersectionObserver' in window) {
        const slideshowObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                const card = entry.target;
                const galleryData = card.getAttribute('data-gallery');
                
                if (!galleryData) return;

                if (entry.isIntersecting) {
                    // Start Slideshow
                    startSlideshow(card, JSON.parse(galleryData));
                } else {
                    // Stop Slideshow
                    stopSlideshow(card);
                }
            });
        }, {
            threshold: 0.7 // Trigger when 70% visible
        });

        document.querySelectorAll('.product-card-mobile[data-gallery]').forEach(card => {
            slideshowObserver.observe(card);
        });
    }

    function startSlideshow(card, images) {
        if (card.dataset.intervalId) return; // Already running
        
        // Preload images logic could be added here for even smoother exp
        
        let currentIndex = 0;
        const imgDiv = card.querySelector('.mobile-slideshow-img');
        
        // Try to find current index based on current bg (optional, or just start loop)
        
        card.dataset.intervalId = setInterval(() => {
            currentIndex = (currentIndex + 1) % images.length;
            const nextUrl = images[currentIndex];
            
            // Preload next image
            const img = new Image();
            img.src = nextUrl;
            img.onload = () => {
                 imgDiv.style.backgroundImage = `url('${nextUrl}')`;
            };
            
        }, 4500); // 4.5 seconds
    }

    function stopSlideshow(card) {
        if (card.dataset.intervalId) {
            clearInterval(card.dataset.intervalId);
            delete card.dataset.intervalId;
            // Optionally reset to first image
        }
    }
});
</script>
@endonce

          @endif
        @endforeach

      </div>



    </div>
  </section>

  <div class="col-12 video">
    <iframe width="100%" height="600px"
      src="https://www.youtube.com/embed/{{$home->video}}?loop=1&autoplay=1&playlist={{$home->video}}"
      title="YouTube video player" frameborder="0"
      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
      allowfullscreen></iframe>
  </div>

@endsection

{{-- =========================================================
SCRIPT Y ESTILOS DE FILTRADO
========================================================= --}}
@section('scripts')
  <script>
    function toggleBuscadorMobile() {
      // Ahora buscamos por CLASE, no por ID, porque tenemos 2 bloques separados
      const elements = document.querySelectorAll('.buscador-collapsible');
      const chevron = document.getElementById('icon-chevron');
      
      // Tomamos el estado del primer elemento como referencia
      const isHidden = elements[0].classList.contains('d-none');

      elements.forEach(el => {
        if (isHidden) {
          el.classList.remove('d-none');
        } else {
          el.classList.add('d-none');
        }
      });

      if (isHidden) {
        chevron.style.transform = 'rotate(180deg)';
      } else {
        chevron.style.transform = 'rotate(0deg)';
      }
    }

    document.addEventListener('DOMContentLoaded', function () {

      // === REFERENCIAS DOM ===
      const selectAncho = document.getElementById('filtro-ancho');
      const selectMedida = document.getElementById('filtro-medida');
      const selectEspesor = document.getElementById('filtro-espesor');
      const labelEspesor = document.getElementById('label-espesor');
      const btnLimpiar = document.getElementById('btn-limpiar-filtros');
      const msgSinResult = document.getElementById('msg-sin-resultados');
      const btnsFamilia = document.querySelectorAll('.btn-familia');
      const cards = document.querySelectorAll('.card-buscable');

      // Token de seguridad de Laravel (Necesario para enviar datos)
      const csrfToken = "{{ csrf_token() }}";

      // Estado inicial
      let familiaActiva = '1';
      let timeoutChismoso = null; // Para el temporizador del espa

      // ======================================================
      // "97"1'5?69"1'5 EL CHISMOSO: Funcin para guardar bsquedas
      // ======================================================
      function activarElChismoso() {
        // 1. Si ya haba un envo pendiente, lo cancelamos (reinicia el reloj)
        clearTimeout(timeoutChismoso);

        // 2. Esperamos 3 segundos antes de guardar (Debounce)
        timeoutChismoso = setTimeout(() => {

          // Validamos que haya seleccionado al menos ALGO (para no guardar vacos)
          if (selectAncho.value === '' && selectMedida.value === '' && selectEspesor.value === '') {
            return; // Si no eligi nada, no guardamos.
          }

          // 3. Preparamos los datos
          // Sacamos el TEXTO de la opcin seleccionada (ej: "10 x 10 mm"), no el ID
          const textoAncho = selectAncho.selectedIndex > 0 ? selectAncho.options[selectAncho.selectedIndex].text : null;
          const textoMedida = selectMedida.selectedIndex > 0 ? selectMedida.options[selectMedida.selectedIndex].text : null;
          const textoEspesor = selectEspesor.selectedIndex > 0 ? selectEspesor.options[selectEspesor.selectedIndex].text : null;
          const nombreFamilia = (familiaActiva == '1') ? 'Mallas Electrosoldadas' : 'Metal Desplegado';

          // 4. Enviamos los datos al servidor (AJAX)
          fetch('/guardar-busqueda', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
              familia: nombreFamilia,
              ancho_texto: textoAncho,
              medida_texto: textoMedida,
              espesor_texto: textoEspesor
            })
          })
            .then(response => {
              // Si quieres ver en la consola si funcion (F12):
              // console.log("Chismoso reportando: Guardado con xito."); 
            })
            .catch(error => console.error('Error del chismoso:', error));

        }, 3000); // 3000 ms = 3 segundos de espera
      }


      // ======================================================
      // ?75"1'5 L?0^7GICA DE FILTRADO (Visual)
      // ======================================================

      function filtrarOpcionesDesplegables() {
        const selects = [selectAncho, selectMedida, selectEspesor];
        selects.forEach(sel => {
          const opciones = sel.querySelectorAll('option');
          opciones.forEach(opt => {
            const fam = opt.getAttribute('data-familia');
            if (fam === 'all') {
              opt.style.display = '';
              return;
            }
            // Nota: Usamos '==' para que coincida string con numero
            if (fam == familiaActiva) {
              opt.style.display = '';
              opt.disabled = false;
            } else {
              opt.style.display = 'none';
              opt.disabled = true;
            }
          });
          // Resetear si la opcin elegida qued oculta
          const opcionActual = sel.options[sel.selectedIndex];
          if (opcionActual && opcionActual.getAttribute('data-familia') !== 'all' && opcionActual.getAttribute('data-familia') != familiaActiva) {
            sel.value = '';
          }
        });
      }

      function aplicarFiltros() {
        const valAncho = selectAncho.value;
        const valMedida = selectMedida.value;
        const valEspesor = selectEspesor.value;
        let visibles = 0;

        cards.forEach(card => {
          const cardFamilia = card.getAttribute('data-familia');
          const cardAncho = card.getAttribute('data-ancho');
          const cardMedida = card.getAttribute('data-medida');
          const cardEspesor = card.getAttribute('data-espesor');

          const matchFamilia = (cardFamilia == familiaActiva);
          const matchAncho = (valAncho === '' || valAncho === cardAncho);
          const matchMedida = (valMedida === '' || valMedida === cardMedida);
          const matchEspesor = (valEspesor === '' || valEspesor === cardEspesor);

          if (matchFamilia && matchAncho && matchMedida && matchEspesor) {
            card.classList.remove('filtro-oculto');
            if (card.classList.contains('col-md-3')) {
              card.classList.add('d-md-flex');
            } else {
              card.classList.add('d-flex');
            }
            visibles++;
          } else {
            card.classList.add('filtro-oculto');
            card.classList.remove('d-flex', 'd-md-flex');
          }
        });

        // 2. Filtrar Headers (Lógica adicional para ocultar separadores vacíos)
        const container = document.getElementById('grid-productos');
        let currentHeader = null;

        // Iteramos sobre los hijos directos para respetar el orden DOM
        Array.from(container.children).forEach(child => {
          // Caso: Es un Header
          if (child.classList.contains('header-mobile-categoria')) {
            currentHeader = child;
            // Por defecto lo ocultamos, hasta encontrar un producto visible
            child.classList.add('filtro-oculto');
            child.classList.remove('d-flex');
          }
          // Caso: Es una Card de producto
          else if (child.classList.contains('card-buscable')) {
            // Si la card es visible...
            if (!child.classList.contains('filtro-oculto')) {
              // ...y tenemos un header pendiente de mostrar
              if (currentHeader) {
                currentHeader.classList.remove('filtro-oculto');
                currentHeader.classList.add('d-flex');
                // Ya mostramos el header de este grupo, no necesitamos activarlo de nuevo
                currentHeader = null;
              }
            }
          }
        });

        if (visibles === 0) {
          msgSinResult.classList.remove('d-none');
        } else {
          msgSinResult.classList.add('d-none');
        }

        // ?72 ?0"3AQU?0^1 ACTIVAMOS AL ESP?0^1A! Cada vez que se filtra, preparamos el reporte.
        activarElChismoso();
      }

      // --- EVENTOS ---
      // --- L?0^7GICA DE LOS BOTONES DE FAMILIA ---
      btnsFamilia.forEach(btn => {
        btn.addEventListener('click', function () {
          // 1. Obtenemos la familia seleccionada
          familiaActiva = this.getAttribute('data-familia-id');

          // 2. Visual botones (Pintar el activo)
          btnsFamilia.forEach(b => b.classList.remove('active'));
          this.classList.add('active');

          // 3. CAMBIO DE TEXTOS (Ttulo y Label)
          const tituloFamilia = document.getElementById('titulo-familia');

          if (familiaActiva == '1') {
            // Si es Mallas
            labelEspesor.textContent = 'ESPESOR (Alambre)';
            tituloFamilia.textContent = 'Mallas Electrosoldadas'; // <--- CAMBIO AQU?0^1
          } else {
            // Si es Metal
            labelEspesor.textContent = 'ESPESOR';
            tituloFamilia.textContent = 'Metal Desplegado'; // <--- CAMBIO AQU?0^1
          }

          // 4. Filtrar opciones de los desplegables
          filtrarOpcionesDesplegables();

          // 5. Resetear valores y aplicar
          selectAncho.value = '';
          selectMedida.value = '';
          selectEspesor.value = '';
          aplicarFiltros();
        });
      });

      const selects = [selectAncho, selectMedida, selectEspesor];
      selects.forEach(sel => sel.addEventListener('change', aplicarFiltros));

      btnLimpiar.addEventListener('click', function () {
        selectAncho.value = '';
        selectMedida.value = '';
        selectEspesor.value = '';
        aplicarFiltros();
      });

      // Inicializacin
      filtrarOpcionesDesplegables();
      // Ejecutamos una vez sin activar el chismoso (para que cargue limpio)
      // aplicarFiltros llama al chismoso, pero como los valores estn vacos, el chismoso no guarda nada.
      aplicarFiltros();
    });
  </script>

  <style>
    .filtro-oculto {
      display: none !important;
    }

    /* Estilos del candado (Selects) */
    #filtro-ancho,
    #filtro-medida,
    #filtro-espesor {
      font-weight: 400 !important;
      font-family: 'Open Sans', sans-serif !important;
      color: #555 !important;
      border: 1px solid #ced4da !important;
      outline: none !important;
      box-shadow: none !important;
    }

    #filtro-ancho option,
    #filtro-medida option,
    #filtro-espesor option {
      font-weight: 400 !important;
    }

    /* Estilos Header Video Responsivo */
    .video-header-container {
      height: clamp(250px, 55vh, 700px);
    }

    @media (max-width: 768px) {
      .video-header-container {
        /* Altura reducida en móvil para mostrar buscador */
        height: 35vh;
        min-height: 200px;
        /* Evitar que sea demasiado chico */
      }

      .container-productos {
        margin-top: 15px !important;
      }
    }
  </style>


  <script>
    document.addEventListener("DOMContentLoaded", function () {
      var lazyBackgrounds = [].slice.call(document.querySelectorAll(".lazy-bg"));

      if ("IntersectionObserver" in window) {
        let lazyBackgroundObserver = new IntersectionObserver(function (entries, observer) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              entry.target.style.backgroundImage = entry.target.dataset.bg;
              lazyBackgroundObserver.unobserve(entry.target);
            }
          });
        });

        lazyBackgrounds.forEach(function (lazyBackground) {
          lazyBackgroundObserver.observe(lazyBackground);
        });
      } else {
        // Fallback para navegadores viejos
        lazyBackgrounds.forEach(function (bg) {
          bg.style.backgroundImage = bg.dataset.bg;
        });
      }
    });

    document.addEventListener('DOMContentLoaded', function () {
      const videoElement = document.getElementById('header-video');

      // 1. Definir las dos listas de reproducción
      const playlistDesktop = [
        "{{ asset('videos/vid1.mp4') }}",
        "{{ asset('videos/vid2.mp4') }}",
        "{{ asset('videos/vid3.mp4') }}",
        "{{ asset('videos/vid4.mp4') }}",
        "{{ asset('videos/vid5.mp4') }}",
        "{{ asset('videos/vid6.mp4') }}",
        "{{ asset('videos/vid7.mp4') }}",
        "{{ asset('videos/vid8.mp4') }}"
      ];

      // Always use desktop playlist for quality and full-width mobile view
      const activePlaylist = playlistDesktop;

      let currentVideoIndex = 0;

      if (videoElement) {
        // Cargar el primer video correcto al iniciar
        videoElement.src = activePlaylist[0];
        // Forzar play por si acaso el navegador lo detiene al cambiar src
        videoElement.play().catch(e => { /* Autoplay policies */ });

        // Al terminar un video, pasar al siguiente de la lista activa
        videoElement.addEventListener('ended', function () {
          currentVideoIndex++;
          if (currentVideoIndex >= activePlaylist.length) {
            currentVideoIndex = 0; // Volver al primero (Loop general)
          }

          // Cambiar el src y reproducir
          videoElement.src = activePlaylist[currentVideoIndex];
          videoElement.play().catch(e => { /* Silenced AbortError */ });
        });
      }
    });
  </script>

@endsection