@extends('layouts.plantilla')

@section('title', 'Orden de compra')

@section('content')

  {{--
  Antes aquí había un bloque que obligaba a registrarse/iniciar sesión:
  @if (auth()->guard('usuario')->check()) ... @else ... @endif
  Lo quitamos para habilitar el checkout de invitado.
  --}}

  <section class="section-carrito">
    <div class="container">

      <carrito target="{{ route('web.envio_pedido') }}" ref="carrito" {{-- Forzamos login=1 para que el componente NO
        bloquee a invitados --}} login="1" ruta-productos="{{ route('web.productos.productos2', $familia_1) }}"
        envios="{{ $envios }}" parrafo-envio-fabrica="{{ $configpedido->parrafo_envio_fabrica }}"
        parrafo-envio-interior="{{ $configpedido->parrafo_envio_interior }}"
        parrafo-envio-caba="{{ $configpedido->parrafo_envio_caba }}"
        parrafo-envio-expreso="{{ $configpedido->parrafo_envio_expreso }}"
        costo-envio-fabrica="{{ $configpedido->costo_envio_fabrica }}"
        costo-envio-interior="{{ $configpedido->costo_envio_interior }}"
        costo-envio-caba="{{ $configpedido->costo_envio_caba }}"
        costo-envio-expreso="{{ $configpedido->costo_envio_expreso }}"
        parrafo-efectivo="{{ $configpedido->parrafo_efectivo }}"
        parrafo-transferencia="{{ $configpedido->parrafo_transferencia }}" parrafo-mp="{{ $configpedido->parrafo_mp }}"
        descuento-efectivo="{{ $configpedido->descuento_efectivo }}"
        descuento-transferencia="{{ $configpedido->descuento_transferencia }}"
        descuento-mp="{{ $configpedido->descuento_mp }}" />

    </div>
  </section>

@endsection

{{-- ====== Aviso claro si intentan continuar sin elegir destino (cuando es Envío a domicilio) ====== --}}
@section('scripts')
  <script>
    (function () {
      function ensureBanner() {
        var b = document.getElementById('ff-alert');
        if (b) return b;
        b = document.createElement('div');
        b.id = 'ff-alert';
        b.className = 'alert alert-danger shadow';
        b.role = 'alert';
        b.style.cssText = 'display:none; position:fixed; top:16px; left:50%; transform:translateX(-50%); z-index:2147483647;';
        b.textContent = 'Mensaje';
        document.body.appendChild(b);
        return b;
      }
      function show(msg) {
        var a = ensureBanner();
        a.textContent = msg;
        a.style.display = 'block';
        clearTimeout(a._t);
        a._t = setTimeout(function () { a.style.display = 'none'; }, 3500);
      }
      function killOverlays() {
        try {
          document.querySelectorAll('.modal-backdrop, .sweet-overlay, .vld-overlay, [data-overlay]')
            .forEach(el => el.remove());
          document.body.classList.remove('modal-open');
          document.body.style.removeProperty('padding-right');
        } catch (e) { }
      }

      function findContinuar() {
        return Array.from(document.querySelectorAll('button, a, input[type="button"], input[type="submit"]'))
          .find(el => /continuar\s*compra/i.test((el.textContent || el.value || '').trim()));
      }
      function getEnvioDomRadio() {
        return document.querySelector('input[type="radio"][name="envio"][value="domicilio"]') ||
          Array.from(document.querySelectorAll('input[type="radio"]'))
            .find(r => /domicil/i.test(r.value || '') || /domicil/i.test(r.nextElementSibling?.textContent || ''));
      }
      function getDestinoSelect() {
        // Tomamos el select que luce como el de destinos (placeholder/label “Seleccione un Destino”, “Destino”, “Zona”, etc.)
        return Array.from(document.querySelectorAll('select')).find(s => {
          var txt = ((s.previousElementSibling?.textContent) || '') + ' ' + (s.options[0]?.text || '');
          return /destin|localidad|zona|seleccione/i.test(txt);
        }) || document.querySelector('select');
      }
      function validate() {
        var envioDom = getEnvioDomRadio();
        var destino = getDestinoSelect();
        if (envioDom && envioDom.checked) {
          if (!destino || !destino.value || destino.selectedIndex === 0) {
            killOverlays();
            show('Seleccioná un destino para calcular el envío.');
            alert('Seleccioná un destino para calcular el envío.');
            return false;
          }
        }
        return true;
      }

      function wire() {
        var btn = findContinuar();
        if (!btn) { setTimeout(wire, 600); return; }
        if (btn._wired) return;
        btn._wired = true;
        btn.addEventListener('click', function (e) {
          if (!validate()) {
            e.preventDefault();
            e.stopPropagation();
            if (e.stopImmediatePropagation) e.stopImmediatePropagation();
            return false;
          }
        }, true); // capture=true para adelantarnos a otros handlers
      }

      document.addEventListener('DOMContentLoaded', wire);
      setTimeout(wire, 200);
      setTimeout(wire, 1000);
      setTimeout(wire, 3000);
    })();
  </script>
@endsection

{{-- CSS FINAL: ESTILIZAR NUEVO Y MATAR AL VIEJO --}}
<style>
  /* =========================================
       1. MATAR AL TACHITO FEO (.ff-del)
       ========================================= */
  button.ff-del,
  .ff-del {
    display: none !important;
    /* ¡Desaparece! */
    opacity: 0 !important;
    visibility: hidden !important;
    width: 0 !important;
    height: 0 !important;
    pointer-events: none !important;
  }

  /* =========================================
       2. ESTILIZAR EL NUEVO (.btn-x)
       ========================================= */
  .section-carrito tbody .btn-x {
    /* Limpieza base */
    color: transparent !important;
    font-size: 0 !important;
    border: none !important;
    background-color: transparent !important;
    box-shadow: none !important;

    /* Tamaño y posición */
    width: 24px !important;
    height: 24px !important;
    display: inline-block !important;
    cursor: pointer;

    /* IMAGEN DEL TACHO NUEVO (SVG) */
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23999999' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='3 6 5 6 21 6'%3E%3C/polyline%3E%3Cpath d='M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2'%3E%3C/path%3E%3Cline x1='10' y1='11' x2='10' y2='17'%3E%3C/line%3E%3Cline x1='14' y1='11' x2='14' y2='17'%3E%3C/line%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: center center !important;
    background-size: 20px !important;
  }

  /* Quitar fantasmas del nuevo */
  .section-carrito tbody .btn-x::before,
  .section-carrito tbody .btn-x::after {
    display: none !important;
  }

  /* Efecto rojo al pasar el mouse (Hover) */
  .section-carrito tbody .btn-x:hover {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23dc3545' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='3 6 5 6 21 6'%3E%3C/polyline%3E%3Cpath d='M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2'%3E%3C/path%3E%3Cline x1='10' y1='11' x2='10' y2='17'%3E%3C/line%3E%3Cline x1='14' y1='11' x2='14' y2='17'%3E%3C/line%3E%3C/svg%3E") !important;
  }
</style>


{{-- Script de Google Maps para Autocomplete --}}
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDssmltsmUd-dlEzYjO5VZG72km7ZKtbdU&libraries=places"></script>

