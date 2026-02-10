<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu pedido está listo - Ferrindep</title>
    <style>
        /* RESET Y ESTILOS BASE (Mismos que pedido.blade.php) */
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

        .main-container {
            max-width: 650px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

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

        .content {
            padding: 40px 30px;
            text-align: center;
        }

        .btn-action {
            background-color: #F37021;
            color: #ffffff;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            display: inline-block;
            margin-top: 20px;
        }

        .address-box {
            background-color: #f8f9fa;
            border: 1px dashed #ccc;
            padding: 20px;
            border-radius: 4px;
            margin: 20px 0;
            text-align: left;
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
                                <h1>PEDIDO #{{ $pedido['id'] }} LISTO PARA RETIRAR</h1>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="content">

                    <h2 style="color: #333; margin-top: 0;">¡Hola {{ $nombre }}!</h2>
                    <p style="font-size: 16px; color: #666; line-height: 1.5;">
                        Su pedido <strong>#{{ $pedido['id'] }}</strong> ya se encuentra preparado y listo para retirar
                        por nuestro depósito.
                    </p>

                    <div class="address-box">
                        <strong style="color: #F37021; text-transform: uppercase; font-size: 14px;">📍 Dónde
                            retirar:</strong>
                        <p style="margin: 10px 0 5px; font-size: 15px; font-weight: bold; color: #333;">
                            Retiro de pedidos calle Alsina 2520 (porton negro), José León Suarez, Buenos Aires.
                        </p>
                        <p style="margin: 0; font-size: 14px; color: #666;">
                            Lunes a Viernes 8 a 12hs - 13 a 17hs. Sábados 8 a 12hs.
                        </p>
                    </div>

                    <div class="section-heading"
                        style="text-align: left; margin-top: 30px; border-bottom: 1px solid #eee; padding-bottom: 5px; font-weight: bold; color: #333;">
                        Detalle del pedido preparado
                    </div>

                    <table class="prod-table"
                        style="width: 100%; border-collapse: collapse; margin-top: 15px; text-align: left;">
                        <thead>
                            <tr>
                                <th style="padding: 8px; border-bottom: 2px solid #ddd; color: #777; font-size: 12px;">
                                    Descripción</th>
                                <th
                                    style="padding: 8px; border-bottom: 2px solid #ddd; color: #777; font-size: 12px; text-align: center;">
                                    Cant.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cart as $item)
                                <tr>
                                    <td style="padding: 10px 8px; border-bottom: 1px solid #f2f2f2;">
                                        <span
                                            style="display: block; font-weight: bold; color: #333;">{{ $item['familia'] ?? '' }}
                                            {{ $item['nombre'] ?? '' }}</span>
                                        <span style="display: block; font-size: 12px; color: #777;">
                                            {{ $item['medidas'] ?? '' }}
                                            @if(!empty($item['ancho'])) | {{ $item['ancho'] }}cm @endif
                                            @if(!empty($item['espesor'])) | {{ $item['espesor'] }} @endif
                                            @if(!empty($item['metros'])) ({{ $item['metros'] }}m) @endif
                                        </span>
                                    </td>
                                    <td style="padding: 10px 8px; border-bottom: 1px solid #f2f2f2; text-align: center;">
                                        {{ $item['cantidad'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <p style="margin-top: 30px; margin-bottom: 20px;">
                        ¿Querés ver el detalle completo de tu compra?
                    </p>

                    <a href="https://www.ferrindep.com.ar/mis-compras" class="btn-action">IR A MIS PEDIDOS</a>

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