<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Pedido - Ferrindep</title>
    <style>
        /* RESET Y ESTILOS BASE */
        body {
            margin: 0;
            padding: 0;
            background-color: #eaebed;
            font-family: 'Arial', sans-serif;
            color: #444;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table {
            border-spacing: 0;
            border-collapse: collapse;
        }

        img {
            border: 0;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            display: block;
        }

        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #eaebed;
            padding-bottom: 40px;
        }

        /* CONTENEDOR PRINCIPAL */
        .main-container {
            max-width: 650px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        /* HEADER NEGRO PURO */
        .header {
            background-color: #000000;
            padding: 20px 30px;
            border-bottom: 4px solid #F37021;
        }

        .header-table {
            width: 100%;
        }

        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .info-bar {
            background-color: #f8f9fa;
            border-bottom: 1px solid #eeeeee;
            padding: 10px 30px;
            font-size: 12px;
            color: #777;
            text-align: right;
        }

        .content {
            padding: 30px;
        }

        .section-heading {
            color: #F37021;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
            margin-bottom: 15px;
            margin-top: 25px;
        }

        /* CAJA DE PAGO */
        .payment-alert {
            background-color: #fff8e1;
            border-left: 4px solid #F37021;
            padding: 15px;
            border-radius: 4px;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .payment-alert strong {
            color: #d84315;
        }

        /* ESTILOS RESPONSIVOS PARA COLUMNAS */
        .two-col {
            width: 100%;
            margin-bottom: 20px;
        }

        .column {
            width: 50%;
            vertical-align: top;
            padding-right: 10px;
        }

        @media screen and (max-width: 600px) {
            .column {
                display: block !important;
                width: 100% !important;
                padding-right: 0 !important;
                margin-bottom: 20px !important;
            }

            .header {
                padding: 15px !important;
            }

            .content {
                padding: 15px !important;
            }

            .header h1 {
                font-size: 16px !important;
            }
        }

        .data-label {
            font-size: 11px;
            color: #999;
            font-weight: bold;
            text-transform: uppercase;
            display: block;
            margin-bottom: 3px;
        }

        .data-value {
            font-size: 14px;
            color: #333;
            margin-bottom: 12px;
            line-height: 1.4;
            word-wrap: break-word;
        }

        /* TABLA PRODUCTOS */
        .prod-table {
            width: 100%;
            margin-top: 10px;
        }

        .prod-table th {
            text-align: left;
            font-size: 11px;
            color: #888;
            text-transform: uppercase;
            padding: 10px 0;
            border-bottom: 2px solid #eee;
        }

        .prod-table td {
            padding: 12px 0;
            border-bottom: 1px solid #f2f2f2;
            vertical-align: top;
            font-size: 14px;
        }

        .prod-desc {
            font-weight: bold;
            color: #333;
            display: block;
        }

        .prod-detail {
            font-size: 12px;
            color: #777;
            display: block;
            margin-top: 2px;
        }

        /* TOTALES */
        .totals-area {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 6px;
            margin-top: 30px;
        }

        .total-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
            color: #555;
        }

        .grand-total {
            border-top: 2px solid #ddd;
            margin-top: 15px;
            padding-top: 15px;
            display: flex;
            justify-content: space-between;
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        .grand-total span:last-child {
            color: #F37021;
        }

        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #999;
            background-color: #eaebed;
        }

        .footer a {
            color: #777;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <div class="wrapper">
        <center>
            <div class="main-container">

                <div class="header">
                    <table class="header-table">
                        <tr>
                            <td align="left" valign="middle">
                                <img src="https://www.ferrindep.com.ar/storage/imagenes/mnpD6XMkFoWZ0fLaCim4V1TbbL7lwH2XhEiFYhkp.png"
                                    alt="Ferrindep" width="120" style="display: block; max-width: 120px;">
                            </td>
                            <td align="right" valign="middle">
                                <h1>ORDEN DE COMPRA <span style="color:#F37021;">#{{$pedido['id']}}</span></h1>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="info-bar">
                    <span>Fecha: {{ date('d/m/Y') }}</span>
                </div>

                <div class="content">

                    <p style="font-size: 15px;">Hola
                        <strong>{{$pedido['usuario_nombre'] ?? $pedido['nombre']}}</strong>,
                    </p>

                    <!-- BANNER SEGUIMIENTO -->
                    <div
                        style="background-color: #333333; color: #ffffff; padding: 20px; border-radius: 4px; text-align: center; margin-bottom: 30px;">
                        <h2 style="margin: 0 0 15px 0; font-size: 18px; font-weight: normal; color: #ffffff;">Consulte
                            el estado de su pedido aquí</h2>
                        <a href="https://www.ferrindep.com.ar/mis-compras" target="_blank"
                            style="background-color: #F37021; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 14px; display: inline-block;">VER
                            MI PEDIDO AHORA &rarr;</a>
                    </div>

                    <p style="margin-bottom: 25px; color:#666;">Gracias por elegir Ferrindep. Aquí está el detalle de su
                        solicitud.</p>

                    <table class="two-col">
                        <tr>
                            <td class="column">
                                <div class="section-heading" style="margin-top:0;">Cliente</div>

                                <span class="data-label">Nombre / Razón Social</span>
                                <div class="data-value">{{$pedido['nombre']}}</div>

                                <span class="data-label">Documento</span>
                                <div class="data-value">{{$pedido['dni']}}</div>

                                <span class="data-label">Contacto</span>
                                <div class="data-value">
                                    <a href="mailto:{{$pedido['email']}}"
                                        style="color:#333; text-decoration:none;">{{$pedido['email']}}</a>
                                    <br>{{$pedido['celular']}}
                                </div>
                            </td>

                            <td class="column">
                                <div class="section-heading" style="margin-top:0;">Entrega</div>

                                @if($pedido['envio'] == 'fabrica')
                                    <span class="data-label">Método</span>
                                    <div class="data-value">Retiro en Depósito</div>
                                    <span class="data-label">Dirección de Retiro</span>
                                    <div class="data-value">{!!$pedido['descFabrica']!!}</div>
                                    <div class="data-value" style="font-size:12px; color:#777;">
                                        {!!$pedido['mail_fabrica']!!}
                                    </div>
                                @else
                                    <span class="data-label">Método</span>
                                    <div class="data-value">Envío a Domicilio</div>

                                    <span class="data-label">Destino / Zona</span>
                                    <div class="data-value">{{ $pedido['localidad_envio'] ?? $pedido['descCaba'] }}</div>

                                    <span class="data-label">Dirección de Entrega</span>
                                    <div class="data-value">
                                        {{$pedido['direccion']}}<br>
                                        {{$pedido['localidad']}}, {{$pedido['provincia']}}<br>
                                        CP: {{$pedido['cp']}}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    </table>

                    @if($pedido['mensaje'])
                        <div
                            style="margin-top:25px; padding-top:15px; border-top:1px dashed #ddd; font-size:13px; color:#666;">
                            <strong>Nota del cliente:</strong> {{$pedido['mensaje']}}
                        </div>
                    @endif

                    <div class="section-heading">Productos</div>
                    <table class="prod-table">
                        <thead>
                            <tr>
                                <th width="45%">Descripción</th>
                                <th width="10%" style="text-align:center;">Cant.</th>
                                <th width="20%" style="text-align:right;">Precio U.</th>
                                <th width="25%" style="text-align:right;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $valor = 0; ?>
                            @foreach($cart as $value)
                                <tr>
                                    <td>
                                        <span class="prod-desc">{{$value->familia}} {{$value->nombre}}</span>
                                        <span class="prod-detail">{{$value->medidas}} | {{$value->ancho}}cm |
                                            {{$value->espesor}} ({{$value->metros}}m)</span>
                                    </td>
                                    <td style="text-align:center;">{{$value->cantidad}}</td>
                                    <td style="text-align:right;">${{ number_format($value->precio, 2, ',', '.') }}</td>
                                    <td style="text-align:right;">
                                        ${{ number_format($value->cantidad * $value->precio, 2, ',', '.') }}</td>
                                </tr>
                                <?php    $valor = $valor + ($value->cantidad * $value->precio); ?>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="totals-area">
                        <div class="total-line">
                            <span>Subtotal Productos</span>
                            <span>${{ number_format($valor, 2, ',', '.') }}</span>
                        </div>

                        <?php 
                   $remanente = $pedido['total'] - $valor;
if (isset($pedido['recargo_mp']) && $pedido['recargo_mp'] > 0) {
    $costoEnvio = $remanente - $pedido['recargo_mp'];
} else {
    $costoEnvio = $remanente;
}
                ?>

                        @if($costoEnvio > 1)
                            <div class="total-line">
                                <span>Costo de Envío</span>
                                <span>${{ number_format($costoEnvio, 2, ',', '.') }}</span>
                            </div>
                        @endif

                        @if(isset($pedido['recargo_mp']) && $pedido['recargo_mp'] > 0)
                            <div class="total-line" style="color:#d32f2f;">
                                <span>Recargo MercadoPago</span>
                                <span>${{ number_format($pedido['recargo_mp'], 2, ',', '.') }}</span>
                            </div>
                        @endif

                        @if($pedido['descuento_total'] > 0)
                            <div class="total-line" style="color:#2e7d32;">
                                <span>Descuento</span>
                                <span>-${{ number_format($pedido['descuento_total'], 2, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="grand-total">
                            <span>Total</span>
                            <span>${{ number_format($pedido['total'], 2, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="section-heading">Información de Pago</div>

                    @if($pedido['pago'] == 'mp')
                        <div class="payment-alert" style="background-color: #e8f5e9; border-left-color: #4caf50;">
                            <strong>✅ Pagado con Mercado Pago</strong>
                            <div style="margin-top:5px; font-size:13px;">{!!$pedido['mail_mp']!!}</div>
                        </div>
                    @elseif($pedido['pago'] == 'transferencia')
                        <div class="payment-alert">
                            <strong>
                                <img src="https://img.icons8.com/ios-filled/50/F37021/bank-building.png" width="16"
                                    height="16" style="vertical-align: text-bottom; margin-right: 4px;" alt="Banco">
                                Transferencia Bancaria
                            </strong>
                            <br><span style="font-size:12px; color:#666;">Datos bancarios:</span>
                            <div style="margin-top:8px; line-height:1.5;">{!!$pedido['mail_transferencia']!!}</div>
                        </div>
                    @else
                        <div class="payment-alert" style="background-color: #f5f5f5; border-left-color: #777;">
                            <strong>💵 Efectivo / A convenir</strong>
                            <div style="margin-top:5px;">{!!$pedido['mail_efectivo']!!}</div>
                        </div>
                    @endif


                </div>
            </div>
        </center>

        <div class="footer">
            <p>Ferrindep - Mallas Electrosoldadas</p>
            <p><a href="https://www.ferrindep.com.ar" target="_blank">www.ferrindep.com.ar</a></p>
        </div>
    </div>

</body>

</html>