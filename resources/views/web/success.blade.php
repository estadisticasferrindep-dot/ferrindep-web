@extends('layouts.plantilla')

@section('title', '¡Compra Exitosa!')

@section('content')

    <section class="section-clientes" style="padding: 60px 0; background-color: #f4f4f4;">
        <div class="container text-center">

            <div style="margin: 0 auto 20px auto; width: 80px; height: 80px;">
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                    <circle class="path circle" fill="#4CAF50" cx="65.1" cy="65.1" r="62.1" />
                    <polyline class="path check" fill="none" stroke="#FFFFFF" stroke-width="8" stroke-linecap="round"
                        stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 " />
                </svg>
            </div>

            <h1 style="font-family: 'Open Sans', sans-serif; font-weight: 700; margin-bottom: 10px; color: #333333;">
                ¡Gracias por su compra!</h1>
            <p style="font-family: 'Open Sans', sans-serif; color: #666666; margin-bottom: 10px;">Su pedido ha sido
                registrado correctamente.</p>

            <div
                style="background-color: #e3f2fd; border: 1px solid #bbdefb; color: #0d47a1; padding: 12px 20px; border-radius: 50px; font-size: 14px; display: inline-block; margin-top: 10px;">
                Hemos enviado un detalle de su orden de compra por email, <strong>por favor revisar spam</strong>.
            </div>

            <div style="height: 40px;"></div>

            @if(isset($pedido))
                <div
                    style="background-color: #ffffff !important; border: 1px solid #cccccc !important; border-radius: 8px; overflow: hidden; max-width: 700px; margin: 0 auto; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">

                    <div
                        style="background-color: #000000 !important; color: #ffffff !important; padding: 15px 25px; border-bottom: 4px solid #F37021; display: flex; justify-content: space-between; align-items: center;">
                        <span style="text-transform: uppercase; font-size: 14px; font-weight: 600;">ORDEN CONFIRMADA</span>
                        <span style="color: #F37021; font-size: 18px; font-weight: bold;">#{{ $pedido->id }}</span>
                    </div>

                    <div style="padding: 30px; text-align: left;">
                        <div class="row">
                            <div class="col-md-6" style="margin-bottom: 20px;">
                                <span
                                    style="font-size: 11px; color: #999; text-transform: uppercase; font-weight: bold; display: block; margin-bottom: 5px;">Cliente</span>
                                <div style="font-size: 16px; font-weight: 600; color: #333;">
                                    {{ $pedido->usuario_nombre ?? $pedido->nombre }}
                                </div>
                                <div style="font-size: 14px; color: #666;">{{ $pedido->usuario_email ?? $pedido->email }}</div>
                            </div>

                            <div class="col-md-6" style="margin-bottom: 20px;">
                                <span
                                    style="font-size: 11px; color: #999; text-transform: uppercase; font-weight: bold; display: block; margin-bottom: 5px;">Forma
                                    de Entrega</span>
                                <div style="font-size: 16px; font-weight: 600; color: #333;">
                                    @if($pedido->envio == 'fabrica') Retiro en Depósito @else Envío a Domicilio @endif
                                </div>
                                <div style="font-size: 13px; color: #666; margin-top: 3px;">
                                    {{ $pedido->localidad_envio ?? 'CABA / GBA' }}
                                </div>
                                
                                <div style="margin-top: 15px;">
                                     <span style="font-size: 11px; color: #999; text-transform: uppercase; font-weight: bold; display: block; margin-bottom: 5px;">Forma de Pago</span>
                                     <div style="font-size: 16px; font-weight: 600; color: #333;">
                                         @switch($pedido->pago)
                                            @case('mp')
                                                MercadoPago
                                                @break
                                            @case('transferencia')
                                                Transferencia / Depósito
                                                @break
                                            @case('efectivo')
                                                Efectivo
                                                @break
                                            @default
                                                {{ ucfirst($pedido->pago) }}
                                         @endswitch
                                     </div>
                                </div>
                            </div>
                        </div>

                        <hr style="border: 0; border-top: 1px dashed #ddd; margin: 20px 0;">

                        <!-- DETALLE DE PRODUCTOS -->
                        <div style="margin-bottom: 20px;">
                            <span
                                style="font-size: 11px; color: #999; text-transform: uppercase; font-weight: bold; display: block; margin-bottom: 10px;">Detalle
                                del Pedido</span>
                            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                                <thead style="background: #f9f9f9; border-bottom: 2px solid #eee;">
                                    <tr>
                                        <th style="text-align: left; padding: 8px; color: #666; font-size: 12px;">Producto</th>
                                        <th style="text-align: center; padding: 8px; color: #666; font-size: 12px;">Cant.</th>
                                        <th style="text-align: right; padding: 8px; color: #666; font-size: 12px;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pedido->itemsPedidos as $item)
                                        <tr style="border-bottom: 1px solid #eee;">
                                            <td style="padding: 10px 8px;">
                                                <strong
                                                    style="color: #333; font-size: 13px;">{{ $item->familia ?? $item->nombre ?? $item->descripcion }}</strong>
                                                <br>
                                                <span style="font-size: 11px; color: #777;">
                                                    {{ $item->medidas ? $item->medidas . ' | ' : '' }}
                                                    {{ $item->ancho ? $item->ancho . ' | ' : '' }}
                                                    {{ $item->espesor ? '(' . $item->espesor . ') ' : '' }}
                                                    {{ $item->metros ? '(' . $item->metros . ')' : '' }}
                                                </span>
                                            </td>
                                            <td style="text-align: center; padding: 10px 8px;">{{ $item->cantidad }}</td>
                                            <td style="text-align: right; padding: 10px 8px;">
                                                ${{ number_format($item->precio * $item->cantidad, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                    <!-- Costo Envio Caluclado -->
                                    @php
                                        // Calculamos envio como diferencia: Total - (Subtotal + Recargo - Descuento)
                                        // O mas simple: Total - Suma(Productos)
                                        $sumaProductos = 0;
                                        if ($pedido->itemsPedidos) {
                                            foreach ($pedido->itemsPedidos as $p) {
                                                $sumaProductos += $p->precio * $p->cantidad;
                                            }
                                        }
                                        $costoEnvioEstimado = $pedido->total - $sumaProductos;

                                        // Ajuste por recargo MP (si existe)
                                        if (isset($pedido->recargo_mp)) {
                                            $costoEnvioEstimado -= $pedido->recargo_mp;
                                        }
                                        // Ajuste minímo
                                        if ($costoEnvioEstimado < 0)
                                            $costoEnvioEstimado = 0;
                                    @endphp

                                    @if($costoEnvioEstimado > 100)
                                        <tr style="border-bottom: 1px solid #eee; background-color: #fafafa;">
                                            <td style="padding: 10px 8px; color: #555;">Envío</td>
                                            <td style="text-align: center; padding: 10px 8px;">1</td>
                                            <td style="text-align: right; padding: 10px 8px;">
                                                ${{ number_format($costoEnvioEstimado, 2, ',', '.') }}</td>
                                        </tr>
                                    @endif

                                </tbody>
                            </table>
                        </div>

                        <div style="text-align: right; margin-top: 10px;">
                            <span
                                style="font-size: 12px; color: #999; text-transform: uppercase; font-weight: bold; display: block;">Importe
                                Total</span>
                            <div style="font-size: 28px; color: #F37021; font-weight: bold;">
                                ${{ number_format($pedido->total, 2, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            @endif

            <div style="margin-top: 50px; display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">

                <a href="{{ route('web.home') }}"
                    style="background-color: #F37021 !important; color: #ffffff !important; border: none; padding: 12px 30px; border-radius: 50px; font-weight: bold; text-decoration: none; display: inline-block; font-size: 14px;">
                    Volver a la Tienda
                </a>

                <a href="https://wa.me/5491132631520?text=Hola,%20acabo%20de%20realizar%20el%20pedido%20#{{ isset($pedido) ? $pedido->id : '' }}%20y%20quería%20consultar..."
                    target="_blank"
                    style="background-color: #25D366 !important; color: #ffffff !important; border: none; padding: 12px 30px; border-radius: 50px; font-weight: bold; text-decoration: none; display: inline-block; font-size: 14px;">
                    Contactar por WhatsApp
                </a>

                <a href="{{ route('web.mis_compras') }}"
                    style="background-color: #333333 !important; color: #ffffff !important; border: none; padding: 12px 30px; border-radius: 50px; font-weight: bold; text-decoration: none; display: inline-block; font-size: 14px;">
                    Seguir mi Pedido
                </a>

            </div>

        </div>
    </section>

    <style>
        @keyframes popIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }

            80% {
                transform: scale(1.1);
                opacity: 1;
            }

            100% {
                transform: scale(1);
            }
        }

        .section-clientes svg {
            animation: popIn 0.6s cubic-bezier(0.68, -0.55, 0.27, 1.55) forwards;
        }
    </style>

@endsection