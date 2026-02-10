@extends('layouts.plantilla')
<!-- FORCE UPDATE: {{ date('Y-m-d H:i:s') }} -->

@section('title', 'Orden de compra')

@section('content')

  {{--
  Checkout optimizado:
  - Login se pasa como Entero (1/0).
  - Se inyectan datos de sesión (ubicacion-preasignada) para persistencia.
  --}}
  @php
    // FIX: Preferir $ubicacionCliente (inyectado por LocationComposer via IP/Session) 
    // en lugar de leer session() directo, ya que para nuevos visitantes session está vacía 
    // pero $ubicacionCliente tiene la data IP.
    $u = isset($ubicacionCliente) ? $ubicacionCliente : null;
    $ubicacionPreasignada = [
      "cityName" => $u->cityName ?? session("gps_location.cityName"),
      "regionName" => $u->regionName ?? session("gps_location.regionName"),
      "partidoName" => $u->partido ?? $u->partidoName ?? session("gps_location.partido"),
      "shipping_cost" => session("shipping_cost")
    ];
  @endphp

  <section class="section-carrito">
    <div class="container">


      <carrito target="{{ route('web.envio_pedido') }}"
        ruta-productos="{{ route('web.productos.productos2', $familia_1) }}" {{-- Props de Datos Geográficos y Logística
        --}} :envios='@json($envios, JSON_HEX_APOS | JSON_HEX_QUOT)'
        :destinos='@json($destinos, JSON_HEX_APOS | JSON_HEX_QUOT)' :zonas='@json($zonas, JSON_HEX_APOS | JSON_HEX_QUOT)'
        :destinozonas='@json($destinozonas, JSON_HEX_APOS | JSON_HEX_QUOT)'
        :pesozonas='@json($pesozonas, JSON_HEX_APOS | JSON_HEX_QUOT)' {{-- Props de Configuración de Textos (CMS) --}}
        :parrafo-envio-fabrica='@json($configpedido->parrafo_envio_fabrica ?? "", JSON_HEX_APOS | JSON_HEX_QUOT)'
        :parrafo-envio-interior='@json($configpedido->parrafo_envio_interior ?? "", JSON_HEX_APOS | JSON_HEX_QUOT)'
        :parrafo-envio-caba='@json($configpedido->parrafo_envio_caba ?? "", JSON_HEX_APOS | JSON_HEX_QUOT)'
        :parrafo-envio-expreso='@json($configpedido->parrafo_envio_expreso ?? "", JSON_HEX_APOS | JSON_HEX_QUOT)'
        :parrafo-efectivo='@json($configpedido->parrafo_efectivo ?? "", JSON_HEX_APOS | JSON_HEX_QUOT)'
        :parrafo-transferencia='@json($configpedido->parrafo_transferencia ?? "", JSON_HEX_APOS | JSON_HEX_QUOT)'
        :parrafo-mp='@json($configpedido->parrafo_mp ?? "", JSON_HEX_APOS | JSON_HEX_QUOT)' {{-- Props de Costos y
        Descuentos --}} :costo-envio-fabrica="{{ $configpedido->envio_fabrica ?? 0 }}"
        :costo-envio-interior="{{ $configpedido->envio_interior ?? 0 }}"
        :costo-envio-caba="{{ $configpedido->envio_caba ?? 0 }}"
        :costo-envio-expreso="{{ $configpedido->envio_expreso ?? 0 }}"
        :descuento-efectivo="{{ $configpedido->descuento_efectivo ?? 0 }}"
        :descuento-transferencia="{{ $configpedido->descuento_transferencia ?? 0 }}"
        :descuento-mp="{{ $configpedido->descuento_mp ?? 0 }}" {{-- Estado de usuario y Sesión --}}
        :login="{{ auth()->guard('usuario')->check() ? 1 : 0 }}"
        :ubicacion-preasignada='@json($ubicacionPreasignada, JSON_HEX_APOS | JSON_HEX_QUOT)' />

    </div>
  </section>

@endsection

@section('scripts')


  <script>
    (function () {
      // Legacy Overlay Killer & Helper Scripts
      function killOverlays() {
        try {
          document.querySelectorAll('.modal-backdrop, .sweet-overlay, .vld-overlay, [data-overlay]')
            .forEach(el => el.remove());
          document.body.classList.remove('modal-open');
          document.body.style.removeProperty('padding-right');
        } catch (e) { }
      }

      // Initial cleanup
      setTimeout(killOverlays, 1000);
    })();
  </script>
@endsection

<style>
  /* Styling corrections for the new inputs inside Vue */
  .pac-container {
    z-index: 99999 !important;
  }
</style>