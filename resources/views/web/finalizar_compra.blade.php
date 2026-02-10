@extends('layouts.plantilla')
@section('title','Orden de compra')

@section('content')
<section class="section-carrito">
  <div class="container">
    <h2 class="mb-4">Completar los datos para envío o retiro</h2>

    {{-- Cartel flotante para errores --}}
    <div id="ff-alert"
         class="alert alert-danger shadow"
         role="alert"
         style="display:none; position:fixed; top:16px; left:50%; transform:translateX(-50%); z-index:2147483647;">
      Por favor completar los campos para dar curso al pedido.
    </div>

    {{-- Usamos <form> solo como contenedor (NO se envía con submit nativo) --}}
    <form id="ff-form" onsubmit="return false;">
      <div class="row g-3">

        {{-- Datos del comprador (SIN required nativo) --}}
        <div class="col-md-6">
          <input class="form-control" name="usuario_nombre" placeholder="Nombre y Apellido *">
        </div>
        <div class="col-md-6">
          <input class="form-control" name="usuario_dni" placeholder="Ingrese DNI o CUIT">
        </div>
        <div class="col-md-6">
          <input class="form-control" type="email" name="usuario_email" placeholder="Email *">
        </div>
        <div class="col-md-6">
          <input class="form-control" name="usuario_celular" placeholder="Celular">
        </div>

        {{-- Dirección --}}
        <div class="col-md-6">
          <input class="form-control" name="usuario_domicilio" placeholder="Dirección de entrega *">
        </div>
        <div class="col-md-6">
          <input class="form-control" name="usuario_calle" placeholder="Entre calles / piso / dpto">
        </div>
        <div class="col-md-4">
          <input class="form-control" name="usuario_localidad" placeholder="Localidad *">
        </div>
        <div class="col-md-4">
          <input class="form-control" name="usuario_provincia" placeholder="Provincia *">
        </div>
        <div class="col-md-4">
          <input class="form-control" name="usuario_cp" placeholder="Código Postal *">
        </div>

        <div class="col-12">
          <textarea class="form-control" name="observacion" rows="3" placeholder="Notas del pedido / referencias / aclaraciones respecto a la entrega"></textarea>
        </div>

        {{-- Envío --}}
        <div class="col-md-6">
          <label class="form-label d-block">Método de envío *</label>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="envio" value="retiro" id="envio_retiro">
            <label class="form-check-label" for="envio_retiro">Retiro en depósito</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="envio" value="domicilio" id="envio_dom">
            <label class="form-check-label" for="envio_dom">Envío a domicilio</label>
          </div>
        </div>

        {{-- Pago --}}
        <div class="col-md-6">
          <label class="form-label d-block">Medio de pago *</label>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="pago" value="transferencia" id="pago_transf">
            <label class="form-check-label" for="pago_transf">Transferencia / Depósito</label>
            <div class="small text-muted">{{ $configpedido->parrafo_transferencia }}</div>
          </div>
          <div class="form-check mt-2">
            <input class="form-check-input" type="radio" name="pago" value="efectivo" id="pago_efectivo">
            <label class="form-check-label" for="pago_efectivo">Efectivo (solo retiro)</label>
            <div class="small text-muted">{{ $configpedido->parrafo_efectivo }}</div>
          </div>
          <div class="form-check mt-2">
            <input class="form-check-input" type="radio" name="pago" value="mp" id="pago_mp">
            <label class="form-check-label" for="pago_mp">Mercado Pago</label>
            <div class="small text-muted">{{ $configpedido->parrafo_mp }}</div>
          </div>
        </div>

        {{-- arrastre si llegaste con ?total y ?envio --}}
        <input type="hidden" name="total" value="{{ request('total') }}">
        <input type="hidden" name="envio_calculado" value="{{ request('envio') }}">

        <div class="col-12 mt-2">
          <button id="btnSubmitPedido"
                  type="button"
                  class="btn btn-lg btn-warning"
                  style="position:relative; z-index:99999; cursor:pointer;">
            REALIZAR PEDIDO
          </button>
        </div>
      </div>
    </form>
  </div>
</section>

<script>
(function () {
  function $v(name) {
    var el = document.querySelector('[name="'+name+'"]');
    return el ? (el.value || '').trim() : '';
  }
  function $r(name) {
    var el = document.querySelector('input[name="'+name+'"]:checked');
    return el ? el.value : '';
  }
  function focusField(name) {
    var el = document.querySelector('[name="'+name+'"]');
    if (el) {
      el.scrollIntoView({behavior:'smooth', block:'center'});
      try { el.focus(); } catch(_){}
    }
  }
  function show(msg) {
    var a = document.getElementById('ff-alert');
    if (a) {
      a.textContent = msg;
      a.style.display = 'block';
      clearTimeout(a._t);
      a._t = setTimeout(function(){ a.style.display = 'none'; }, 3500);
    }
    // Fallback por si un overlay tapa el alert rojo:
    try { alert(msg); } catch(_){}
  }
  function addHidden(form, name, value) {
    var i = document.createElement('input');
    i.type = 'hidden'; i.name = name; i.value = value;
    form.appendChild(i);
  }

  document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('btnSubmitPedido');
    if (!btn) return;

    // Captura fuerte del click para impedir cualquier navegación ajena
    btn.addEventListener('click', function (e) {
      try { e.preventDefault(); e.stopPropagation(); e.stopImmediatePropagation(); } catch(_){}

      // Validación manual (sin required nativo)
      var oblig = ['usuario_nombre','usuario_email','usuario_domicilio','usuario_localidad','usuario_provincia','usuario_cp'];
      for (var i=0; i<oblig.length; i++) {
        var n = oblig[i];
        if (!$v(n)) {
          show('Por favor completar los campos para dar curso al pedido.');
          focusField(n);
          return; // NO enviamos nada
        }
      }
      if (!$r('envio')) { show('Seleccioná un método de envío.'); return; }
      if (!$r('pago'))  { show('Seleccioná un medio de pago.');  return; }

      // Construimos el POST real a /carrito
      var f = document.createElement('form');
      f.method = 'POST';
      f.action = "{{ route('web.envio_pedido') }}";
      f.style.display = 'none';

      addHidden(f, '_token', "{{ csrf_token() }}");

      ['usuario_nombre','usuario_dni','usuario_email','usuario_celular',
       'usuario_domicilio','usuario_calle','usuario_localidad','usuario_provincia',
       'usuario_cp','observacion'].forEach(function(n){ addHidden(f, n, $v(n)); });

      addHidden(f, 'envio', $r('envio'));
      addHidden(f, 'pago',  $r('pago'));

      addHidden(f, 'total', document.querySelector('[name="total"]')?.value || '');
      addHidden(f, 'envio_calculado', document.querySelector('[name="envio_calculado"]')?.value || '');

      try {
        var sc = localStorage.getItem('cart');
        if (sc) addHidden(f, 'stringCart', sc);
      } catch(_){}

      document.body.appendChild(f);
      HTMLFormElement.prototype.submit.call(f);
    }, true);
  });
})();
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. BÚSQUEDA PERSISTENTE EN TODOS LOS CAMPOS
        setInterval(function() {
            
            // Buscamos TODOS los inputs de DNI (el de escritorio y el de móvil)
            var inputsDNI = document.querySelectorAll('input[name="dni"]');
            
            // Recorremos cada uno que encontremos
            inputsDNI.forEach(function(dniInput, index) {
                
                // Verificamos si este DNI específico ya tiene el casillero pegado abajo
                // Usamos una clase especial 'factura-a-injected' para marcar territorio
                var siguienteElemento = dniInput.nextElementSibling;
                var yaTieneCasillero = siguienteElemento && siguienteElemento.classList.contains('factura-a-injected');
                
                if (!yaTieneCasillero && dniInput.parentNode) {
                    
                    // Creamos el contenedor del Checkbox
                    var container = document.createElement('div');
                    container.className = 'factura-a-injected'; // Marca para no duplicar
                    container.style.marginTop = '10px';
                    container.style.padding = '8px';
                    container.style.backgroundColor = '#fff3cd'; /* Amarillo suave */
                    container.style.border = '1px solid #ffeeba';
                    container.style.borderRadius = '5px';
                    container.style.display = 'flex';
                    container.style.alignItems = 'center';
                    
                    // ID único para cada casillero (hack_0, hack_1...) para que funcionen los labels
                    var uniqueID = 'facturaA_hack_' + index;
                    
                    container.innerHTML = `
                        <input type="checkbox" id="${uniqueID}" class="factura-checkbox-global" style="width: 18px; height: 18px; margin: 0; margin-right: 10px; cursor: pointer;">
                        <label for="${uniqueID}" style="margin: 0; font-size: 13px; color: #856404; cursor: pointer; line-height: 1.2; font-weight: bold;">
                            Requiero factura A previamente para emitir el pago
                        </label>
                    `;
                    
                    // Lo insertamos DEBAJO del input DNI
                    // (Usamos insertBefore en el siguiente para simular insertAfter)
                    if (siguienteElemento) {
                        dniInput.parentNode.insertBefore(container, siguienteElemento);
                    } else {
                        dniInput.parentNode.appendChild(container);
                    }
                }
            });
            
        }, 800); // Revisamos cada casi segundo por si Vue redibuja


        // 2. LÓGICA DEL CLIC (Sincronizada)
        document.body.addEventListener('click', function(e) {
            var target = e.target;
            if (target.tagName !== 'BUTTON') target = target.closest('button');

            // Si hacen clic en REALIZAR PEDIDO
            if (target && target.innerText && (target.innerText.includes('REALIZAR') || target.innerText.includes('PROCESANDO'))) {
                
                // Buscamos si ALGUNO de los casilleros está marcado
                var checkboxes = document.querySelectorAll('.factura-checkbox-global');
                var algunoMarcado = false;
                checkboxes.forEach(function(chk) {
                    if (chk.checked) algunoMarcado = true;
                });
                
                if (algunoMarcado) {
                    // Buscamos las áreas de texto (también puede haber dos)
                    var textAreas = document.querySelectorAll('textarea[name="mensaje"]');
                    
                    textAreas.forEach(function(textArea) {
                        var nota = "🔴 [REQUIERE FACTURA 'A' PREVIA PARA PAGAR] \n";
                        if (!textArea.value.includes("REQUIERE FACTURA")) {
                            textArea.value = nota + textArea.value;
                            textArea.dispatchEvent(new Event('input')); // Avisar a Vue
                        }
                    });
                }
            }
        });

    });
</script>
@endsection
