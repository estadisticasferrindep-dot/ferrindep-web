@extends('layouts.plantilla')

@section('title', $producto->medidas->medidas . ' ' . $producto->espesor->espesor)

@section('content')

{{-- ===== ESTILOS CSS ===== --}}
<style>
/* MOBILE */
@media (max-width: 640px) {
  #product-hero{ width:100% !important; }
  #hero-box{ position: relative !important; width: 100% !important; }
  #hero-box::before{ content:""; display:block; padding-top: 100%; }
  #hero-box > img{ position: absolute !important; inset: 0 !important; width: 100% !important; height: 100% !important; object-fit: contain !important; display: block !important; max-width: none !important; }
  .product-thumbs img{ width: 88px !important; height: 88px !important; object-fit: cover !important; cursor: pointer; }
}
/* DESKTOP */
@media (min-width: 641px) {
  #hero-box{ position: static !important; }
  #hero-box::before{ content: none !important; }
  #product-hero img#foto-principal{ position: static !important; width: 100% !important; max-width: 100% !important; height: auto !important; object-fit: initial !important; display: block !important; }
}
.section-home-categorias #product-hero img#foto-principal{ width: 100% !important; max-width: 100% !important; height: auto !important; }
</style>

<section class="section-equipo section-categoria" style="margin-top:87px;">
  <div class="container">
    <div class="row">

      {{-- SIDEBAR CATEGORÍAS --}}
      <section class="nav-categorias col-12 col-md-3 d-none d-md-flex">
        <div class="list-group list-group-flush">
          @foreach ($familias as $familia)
            <a href="{{route('web.productos.productos2', $familia->id)}}" style="color:#FD914D!important;padding-left:0;font-weight:400;display:flex;justify-content:space-between;" class="d-none d-md-flex cat-no-activa list-group-item list-group-item-action list-caracteristica">
               {{$familia->nombre}} <i class="fas fa-angle-down"></i>
            </a>
            <a href="{{route('web.productos.productos2.mobile', $familia->id)}}" style="color:#FD914D!important;padding-left:0;font-weight:400;display:flex;justify-content:space-between;" class="d-flex d-md-none cat-no-activa list-group-item list-group-item-action list-caracteristica">
               {{$familia->nombre}} <i class="fas fa-angle-down"></i>
            </a>
            @if ($familia->id == $familiaElegida)
              @foreach ($categorias as $item)
                @if ($item->show && $item->tieneProductosFamilia($familia->id))
                  <a href="{{route('web.productos.categoria', [$item->id,$familia->id])}}" class="d-none d-md-flex cat-no-activa list-group-item list-group-item-action list-caracteristica">
                     {{$item->con_nombre ? $item->nombre : (intval($item->nombre) >= 100 ? $item->nombre/100 .' m alto/ancho' : $item->nombre .' cm alto/ancho')}}
                  </a>
                  <a href="{{route('web.productos.categoria.mobile', [$item->id,$familia->id])}}" class="d-flex d-md-none cat-no-activa list-group-item list-group-item-action list-caracteristica">
                     {{$item->con_nombre ? $item->nombre : (intval($item->nombre) >= 100 ? $item->nombre/100 .' m alto/ancho' : $item->nombre .' cm alto/ancho')}}
                  </a>
                @endif
                @foreach ($productos as $item2)
                  @if ($item2->show && $item->id == $item2->categoria_id && $item2->familia_id == $familiaElegida)
                    @if ($item2->con_nombre)
                      <a href="{{ route('web.productos.producto',$item2->id) }}#prod" class="{{ $item2->id == $producto->id ? 'prod-activo ' : 'prod-no-activo '}} list-group-item d-none d-md-flex list-group-item-action list-trabajo" style="padding-left:35px;">{{$item2->nombre}}</a>
                      <a href="{{ route('web.productos.producto.mobile',$item2->id) }}" class="{{ $item2->id == $producto->id ? 'prod-activo ' : 'prod-no-activo '}} list-group-item d-flex d-md-none list-group-item-action list-trabajo" style="padding-left:35px;">{{$item2->nombre}}</a>
                    @else
                      <a href="{{ route('web.productos.producto',$item2->id) }}#prod" class="{{ $item2->id == $producto->id ? 'prod-activo ' : 'prod-no-activo '}} list-group-item d-none d-md-flex list-group-item-action list-trabajo" style="padding-left:35px;">{{$item2->medidas->medidas}} {{$item2->espesor->espesor}}</a>
                      <a href="{{ route('web.productos.producto.mobile',$item2->id) }}" class="{{ $item2->id == $producto->id ? 'prod-activo ' : 'prod-no-activo '}} list-group-item d-flex d-md-none list-group-item-action list-trabajo" style="padding-left:35px;">{{$item2->medidas->medidas}} {{$item2->espesor->espesor}}</a>
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
                if (!$principalUrl) { $principalUrl = $galeriaUrls->first() ?: $PLH; }
              @endphp

              <div id="product-hero" class="product-hero">
                <div id="hero-box" class="ratio-box">
                  <img id="foto-principal" src="{{ $principalUrl }}" alt="{{ $producto->nombre }}" loading="lazy" style="width:100% !important; max-width:100% !important; height:auto !important; display:block !important;">
                </div>
                @if (!$producto->hay_stock)
                  <div class="ribbon ribbon-top-left sin_stock"><span style="background-color:#6c757d;">SIN STOCK</span></div>
                @elseif ($producto->oferta)
                  <div class="ribbon ribbon-top-left"><span>¡OFERTA!</span></div>
                @endif
              </div>

              @if($galeriaUrls->count())
                <div class="d-flex flex-wrap gap-2 mt-3 product-thumbs">
                  @foreach($galeriaUrls as $url)
                    <img src="{{ $url }}" alt="Vista" class="img-thumbnail fm-thumb" loading="lazy" onclick="document.getElementById('foto-principal').src=this.src;">
                  @endforeach
                </div>
              @endif
            </div>

            {{-- COLUMNA DERECHA (Info + Compra) --}}
            <div class="col-12 col-md-7">
              
              @if($producto->id == 1)
                  
                  {{-- ===== DISEÑO NUEVO (PRODUCTO 1) ===== --}}
                  <div>
                      <div class="tabla-trabajo" style="font: normal normal bold 14px/19px Open Sans; letter-spacing:0; color:#939292;">
                        @if($producto->categoria->con_nombre == 1) {{$producto->categoria->nombre}} @else Rollos {{$producto->categoria->nombre}}cm de ancho @endif
                      </div>

                      @if ($producto->con_nombre)
                        <div class="tabla-trabajo" style="font: normal normal bold 28px/38px Open Sans;">{{$producto->nombre}}</div>
                      @else
                        <div class="tabla-trabajo" style="font: normal normal bold 28px/38px Open Sans;">{{$producto->medidas->medidas}} {{$producto->espesor->espesor}}</div>
                      @endif
                      
                      <div class="tabla-trabajo" style="color: #939292; font-size: 14px; margin-top: 3px;">{{$producto->vendidos}} vendidos</div>
                      <hr class="subtitulos">
                      {{-- Descripción con formato HTML --}}
                      <div class="mt-3 mb-2" style="font-size: 15px; line-height: 1.6; color: #333;">{!! $producto->descripcion !!}</div>

                      {{-- CORTADOR VIRTUAL --}}
                      <div id="cortador-virtual-app" class="p-3 border rounded bg-light">
                          <h5 class="mb-3 text-center" style="font-weight:700; color:#444;">
                              <i class="fas fa-ruler-horizontal text-primary"></i> Seleccione Largo del Rollo
                          </h5>

                          <div class="d-flex align-items-center justify-content-between mb-3">
                              <button class="btn btn-outline-secondary btn-sm d-flex align-items-center justify-content-center" onclick="cambiarLargo(-1)" style="width: 38px; height: 38px; flex-shrink: 0;"><i class="fas fa-minus"></i></button>
                              <div class="flex-grow-1 mx-2 position-relative">
                                  <input type="range" class="form-range" id="rangeLargo" min="0" step="1" style="width:100%; cursor: pointer;">
                              </div>
                              <button class="btn btn-outline-secondary btn-sm d-flex align-items-center justify-content-center" onclick="cambiarLargo(1)" style="width: 38px; height: 38px; flex-shrink: 0;"><i class="fas fa-plus"></i></button>
                          </div>

                          <div class="card mb-3 border-0 shadow-sm" style="background:#fff;">
                              <div class="card-body text-center py-2">
                                  <div style="font-size:15px; font-weight:bold; color:#000; line-height: 1.3;">
                                      <span id="txtLargo">--</span>
                                  </div>
                                  <div class="text-muted small">Precio: $<span id="txtPrecioUnit">--</span> / metro</div>
                                  <div class="mt-2 pt-2 border-top" style="color:#FD914D; font-size:22px; font-weight:800;">$<span id="txtTotalRollo">0</span></div>
                              </div>
                          </div>

                          <div class="row g-2">
                              <div class="col-12">
                                  <button class="btn btn-outline-primary w-100" onclick="sumarALista()">
                                      <i class="fas fa-cart-plus"></i> Sumar este rollo al pedido
                                  </button>
                              </div>
                          </div>

                          <div id="lista-acumulada" class="mt-3 d-none">
                              <h6 class="small text-muted fw-bold border-bottom pb-1">Tu selección:</h6>
                              <ul id="items-lista" class="list-group list-group-flush small mb-3"></ul>
                              <div id="mensaje-ayuda-lista" class="text-center my-2 text-muted small" style="font-style: italic; display: none;">
                                  ☝️ Para agregar otro rollo, repetir el mismo paso
                              </div>
                              <button class="btn btn-primary w-100 py-2 fw-bold" style="background:#FD914D; border:none;" onclick="enviarAlCarrito()">
                                  AGREGAR A LA ORDEN DE COMPRA ($<span id="txtGranTotal">0</span>)
                              </button>
                          </div>
                          </div>
                      </div>

                      {{-- CALCULADORA DE ENVÍOS (BETA) --}}
                      <div class="mt-3 p-3 bg-white border rounded shadow-sm">
                          <h6 class="text-center fw-bold text-dark mb-3" style="font-size: 15px;">
                              <i class="fas fa-truck" style="color:#FD914D;"></i> Calculá tu envío
                          </h6>
                          <div class="mb-2">
                              <label for="selectDestino" class="form-label small text-muted">Elige tu localidad:</label>
                              <select id="selectDestino" class="form-select form-select-sm" style="font-size:13px;">
                                  <option value="">Cargando destinos...</option>
                              </select>
                          </div>
                          <button id="btnCalcularEnvio" class="btn btn-sm w-100 fw-bold text-white" style="background:#FD914D; border:none;">
                              CALCULAR COSTO
                          </button>
                          <div id="resultadoEnvio" class="mt-3 text-center fw-bold" style="color:#555; font-size:14px; min-height:20px;"></div>
                      </div>

                      {{-- SCRIPT CALCULADORA --}}
                      <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const selDestino = document.getElementById('selectDestino');
                            const btnCalc = document.getElementById('btnCalcularEnvio');
                            const divRes = document.getElementById('resultadoEnvio');

                            // 1. Cargar Destinos
                            fetch('/api/shipping/destinations')
                                .then(r => r.json())
                                .then(data => {
                                    let html = '<option value="">Selecciona tu localidad...</option>';
                                    data.forEach(d => {
                                        html += `<option value="${d.id}">${d.nombre}</option>`;
                                    });
                                    selDestino.innerHTML = html;
                                })
                                .catch(e => {
                                    selDestino.innerHTML = '<option value="">Error al cargar</option>';
                                    console.error(e);
                                });

                            // 2. Calcular
                            btnCalc.addEventListener('click', function() {
                                const destinoId = selDestino.value;
                                if(!destinoId) {
                                    divRes.innerHTML = '<span class="text-danger">Por favor selecciona una localidad.</span>';
                                    return;
                                }

                                // Necesitamos el ID de la presentación actual seleccionada en el slider
                                // Usamos variables globales del script del cortador o tratamos de acceder al array
                                // En el script original: 'window.cambiarLargo' y 'presentaciones' están en scope local o global?
                                // El script original está más abajo (línea 275).
                                // Para acceder a la presentación actual, necesitamos que el script de abajo exponga 'presentaciones' e 'indiceActual'.
                                // O podemos inferirlo aquí si movemos este bloque, pero mejor lo integramos.
                            });
                        });
                      </script>

                      {{-- LISTA DE PRECIOS COMPLETA (ACORDEÓN) --}}
                      <div class="mt-3">
                        <button class="btn btn-light btn-sm w-100 text-start border d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePrecios" aria-expanded="false" aria-controls="collapsePrecios" style="color: #666;">
                            <span><i class="fas fa-list-ul me-2"></i> Ver lista completa de precios y stock</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="collapse" id="collapsePrecios">
                            <div class="card card-body border-top-0 p-0">
                                <table class="table table-sm table-striped table-hover mb-0" style="font-size: 13px;">
                                    <thead class="table-light"><tr><th class="ps-3">Largo</th><th>Precio</th><th class="text-end pe-3">Disponibilidad</th></tr></thead>
                                    <tbody>
                                        @foreach($producto->presentaciones->sortBy('medida') as $pres)
                                            <tr>
                                                <td class="ps-3 fw-bold">Rollo {{ $pres->medida ?? $pres->metros }}m</td>
                                                <td>$ {{ number_format($pres->precio, 0, ',', '.') }}</td>
                                                <td class="text-end pe-3">
                                                    @if($pres->stock == 1) <span class="badge bg-warning text-dark" style="font-size: 10px;">¡Último!</span>
                                                    @elseif($pres->stock > 0) <span class="text-success"><i class="fas fa-check-circle"></i> Stock</span>
                                                    @else <span class="text-muted">Sin stock</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                      </div>
                  </div>

              @else
                  
                  {{-- ===== DISEÑO VIEJO (OTROS PRODUCTOS) ===== --}}
                  {{-- Aquí está la clave: Volvemos a la sintaxis original {{ }} para que no rompa --}}
                  <div>
                    <div class="tabla-trabajo" style="font: normal normal bold 14px/19px Open Sans; letter-spacing:0; color:#939292;">
                      @if($producto->categoria->con_nombre == 1) {{$producto->categoria->nombre}} @else Rollos {{$producto->categoria->nombre}}cm de ancho @endif
                    </div>
                    @if ($producto->con_nombre)
                      <div class="tabla-trabajo" style="font: normal normal bold 28px/38px Open Sans;">{{$producto->nombre}}</div>
                    @else
                      <div class="tabla-trabajo" style="font: normal normal bold 28px/38px Open Sans;">{{$producto->medidas->medidas}} {{$producto->espesor->espesor}}</div>
                    @endif
                    <add-to-cart 
                        descripcion="{{ $producto->descripcion }}" 
                        medidas="{{ $producto->medidas->medidas }}" 
                        espesor="{{ $producto->espesor->espesor }}" 
                        ancho="{{$producto->categoria->nombre}}" 
                        familia="{{$producto->familia->nombre}}" 
                        con-nombre="{{$producto->con_nombre}}" 
                        vendidos="{{$producto->vendidos}}" 
                        anula-envio="{{$producto->anular_envio}}" 
                        presentaciones="{{ $producto->presentaciones }}" 
                        imagen="{{ $principalUrl }}" 
                        id="{{ $producto->id }}" 
                        oferta="{{$producto->oferta}}" 
                        ruta="/carrito" 
                        ref="me" 
                    />
                  </div>
              @endif

            </div>
          </div>

          <div class="row"><div class="col-12"><h4 style="font: normal normal medium 16px/21px Rubik; letter-spacing:0; color:#505050;">Características técnicas</h4><div class="caracteristicas">{!! $producto->caracteristicas !!}</div></div></div>
          @if($producto->video)
            <div class="row my-5"><div class="col-12"><iframe class="video-prod" width="100%" height="500px" src="https://www.youtube.com/embed/{{$producto->video}}?loop=1&autoplay=1&playlist={{$producto->video}}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div></div>
          @endif
          <div class="row"><div class="col-12"><h4 style="font: normal normal medium 16px/21px Rubik; letter-spacing:0; color:#505050;">Usos</h4><div class="usos">{!! $producto->usos !!}</div></div></div>
        </div>
      </section>
    </div>
  </div>
</section>
@endsection

@section('scripts')
@parent

<style>
@media (max-width: 640px){
  #hero-box{ position: static !important; }
  #hero-box::before{ content: none !important; display: none !important; }
  #hero-box > img, #product-hero img#foto-principal{ position: static !important; width: 100% !important; max-width: 100% !important; height: auto !important; max-height: none !important; object-fit: contain !important; display: block !important; }
}
</style>

<script>
  window.colores   = @json($colores ?? []);
  window.galerias  = @json(($producto->galerias ?? collect())->map(function($g){
    return $g->imagen ? asset(\Illuminate\Support\Facades\Storage::url($g->imagen)) : null;
  })->filter()->values());
</script>

<script>
(function(){
  var img = document.getElementById('foto-principal');
  function fixHeroSize(){ if(!img) return; img.removeAttribute('width'); img.removeAttribute('height'); img.style.width = '100%'; img.style.maxWidth = '100%'; img.style.height = 'auto'; img.style.display = 'block'; }
  function setHeroRatio(){ var box = document.getElementById('hero-box'); if(!img || !box) return; var nw = img.naturalWidth || img.width, nh = img.naturalHeight || img.height; if(nw > 0 && nh > 0){ box.style.setProperty('--pad', (nh / nw * 100) + '%'); } }
  ['load','resize'].forEach(function(ev){ window.addEventListener(ev, function(){ fixHeroSize(); setHeroRatio(); }); });
  document.addEventListener('DOMContentLoaded', function(){ fixHeroSize(); setHeroRatio(); if(img){ img.addEventListener('load', function(){ fixHeroSize(); setHeroRatio(); }); } setTimeout(function(){ fixHeroSize(); setHeroRatio(); }, 1200); });
})();
</script>

{{-- SCRIPT CORTADOR (SOLO PARA PRODUCTO 1) --}}
@if($producto->id == 1)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const txtMedida = "{{ $producto->medidas->medidas }}";
        const txtAncho  = "{{ $producto->categoria->con_nombre ? $producto->categoria->nombre : $producto->categoria->nombre . 'cm' }}";

        let presentacionesRaw = [];
        try { presentacionesRaw = {!! json_encode($producto->presentaciones) !!}; } catch (e) { console.error("Error datos:", e); return; }

        var presentaciones = presentacionesRaw.map(p => {
            let largoEncontrado = p.metros || p.medida || p.longitud || p.cantidad_metros || 0;
            let precioEncontrado = p.precio || p.valor || p.price || 0;
            return { largo: parseFloat(largoEncontrado), precioTotal: parseFloat(precioEncontrado), original: p };
        });
        presentaciones.sort((a,b) => a.largo - b.largo);

        let indiceActual = 0;
        let carritoTemporal = [];
        const range = document.getElementById('rangeLargo');
        if(!range) return;

        range.max = presentaciones.length - 1;
        range.value = Math.floor(presentaciones.length / 2);
        actualizarVista(range.value);

        range.addEventListener('input', (e) => actualizarVista(e.target.value));

        window.cambiarLargo = function(delta) {
            let nuevo = parseInt(range.value) + delta;
            if(nuevo >= 0 && nuevo <= range.max) { range.value = nuevo; actualizarVista(nuevo); }
        };

        window.sumarALista = function() {
            const p = presentaciones[indiceActual];
            const stockReal = parseInt(p.original.stock || 0);
            const cantidadEnLista = carritoTemporal.filter(item => item.original.id === p.original.id).length;

            if ( (cantidadEnLista + 1) > stockReal ) {
                let mensaje = `⚠️ ¡No hay suficiente stock!`;
                if (stockReal === 0) { mensaje += `\nNo quedan unidades disponibles.`; } 
                else if (stockReal === 1) { mensaje += `\nSolo queda 1 unidad disponible.`; } 
                else { mensaje += `\nSolo quedan ${stockReal} unidades.`; }
                alert(mensaje);
                return;
            }

            carritoTemporal.push(p);
            renderizarLista();
            document.getElementById('lista-acumulada').classList.remove('d-none');
        };

        window.borrarDeLista = function(index) {
            carritoTemporal.splice(index, 1);
            renderizarLista();
            if(carritoTemporal.length === 0) { document.getElementById('lista-acumulada').classList.add('d-none'); }
        };
        
        window.enviarAlCarrito = function() {
            if(carritoTemporal.length === 0) { alert("Suma al menos un rollo."); return; }
            let cart = JSON.parse(localStorage.getItem('cartQunuy')) || [];
            carritoTemporal.forEach(itemLista => {
                let existente = cart.find(c => c.id == {{ $producto->id }} && c.presentacionId == itemLista.original.id);
                if (existente) { existente.cantidad = parseInt(existente.cantidad) + 1; }
                else {
                    cart.push({
                        id: {{ $producto->id }}, medidas: "{{ $producto->medidas->medidas }}", espesor: "{{ $producto->espesor->espesor }}",
                        precio: itemLista.original.precio, presentacionId: itemLista.original.id, cantidad: 1, free: itemLista.original.free,
                        stock: itemLista.original.stock, peso: itemLista.original.peso, limite: itemLista.original.limite,
                        nombre: itemLista.original.nombre, metros: itemLista.original.metros, ancho: "{{ $producto->categoria->nombre }}",
                        descripcion: `{!! str_replace(["\r","\n"],"", addslashes($producto->descripcion)) !!}`, imagen: "{{ $principalUrl }}",
                        anulaEnvio: {{ $producto->anular_envio ? 'true' : 'false' }}, familia: "{{ $producto->familia->nombre }}"
                    });
                }
            });
            localStorage.setItem("cartQunuy", JSON.stringify(cart));
            window.location.href = "/carrito";
        };

        function actualizarVista(idx) {
            indiceActual = idx;
            const p = presentaciones[idx];
            let unitario = 0;
            if (p.largo > 0) { unitario = p.precioTotal / p.largo; }
            const fmt = new Intl.NumberFormat('es-AR', { maximumFractionDigits: 0 });
            
            document.getElementById('txtLargo').innerText = `${txtMedida} / ${txtAncho} / ${p.largo} metros`;
            document.getElementById('txtPrecioUnit').innerText = fmt.format(unitario);
            document.getElementById('txtTotalRollo').innerText = fmt.format(p.precioTotal);
        }

        function renderizarLista() {
            const ul = document.getElementById('items-lista');
            const msgAyuda = document.getElementById('mensaje-ayuda-lista');
            ul.innerHTML = '';
            let granTotal = 0;
            const fmt = new Intl.NumberFormat('es-AR', { maximumFractionDigits: 0 });

            carritoTemporal.forEach((item, i) => {
                granTotal += item.precioTotal;
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent';
                li.innerHTML = `
                    <div style="line-height:1.2">
                        <div style="font-weight:bold;font-size:13px;color:#333;">${txtMedida} / ${txtAncho} / Rollo ${item.largo}m</div>
                        <div class="text-muted" style="font-size:12px;">($${fmt.format(item.precioTotal)})</div>
                    </div>
                    <button class="btn btn-sm text-danger p-0" onclick="borrarDeLista(${i})"><i class="fas fa-times"></i></button>
                `;
                ul.appendChild(li);
            });
            document.getElementById('txtGranTotal').innerText = fmt.format(granTotal);
            
            if (msgAyuda) {
                msgAyuda.style.display = (carritoTemporal.length > 0) ? 'block' : 'none';
            }
        }
        }

        // --- LÓGICA CALCULADORA ENVÍO (INTEGRADA) ---
        const selDestino = document.getElementById('selectDestino');
        const btnCalc = document.getElementById('btnCalcularEnvio');
        const divRes = document.getElementById('resultadoEnvio');

        if(selDestino && btnCalc){
            // Cargar destinos
            fetch('/api/shipping/destinations')
                .then(r => r.json())
                .then(data => {
                    let html = '<option value="">Selecciona tu localidad...</option>';
                    data.forEach(d => {html += `<option value="${d.id}">${d.nombre}</option>`;});
                    selDestino.innerHTML = html;
                })
                .catch(e => console.error(e));

            btnCalc.addEventListener('click', function(){
                const destinoId = selDestino.value;
                if(!destinoId) { divRes.innerHTML = '<span class="text-danger">Por favor selecciona una localidad.</span>'; return; }
                
                const presActual = presentaciones[indiceActual];
                if(!presActual) return;

                divRes.innerHTML = '<span style="color:#FD914D"><i class="fas fa-spinner fa-spin"></i> Calculando...</span>';
                
                fetch('/api/shipping/calculate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ destino_id: destinoId, presentacion_id: presActual.original.id })
                })
                .then(r => r.json())
                .then(res => {
                    if(res.error) {
                        divRes.innerHTML = `<span class="text-danger" style="font-size:12px">${res.error}</span>`;
                    } else {
                        const fmt = new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS', maximumFractionDigits: 0 });
                        divRes.innerHTML = `<div class="text-success" style="font-size:15px; border-top:1px solid #eee; padding-top:5px; margin-top:5px;">Costo de envío: <b>${fmt.format(res.costo_envio)}</b></div>`;
                    }
                })
                .catch(e => {
                    console.error(e); 
                    divRes.innerHTML = '<span class="text-danger">Error de conexión.</span>';
                });
            });
        }
    });
</script>
@endif
@endsection