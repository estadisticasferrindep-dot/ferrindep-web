<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Alerta de Seguimiento</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">

    <h2>Alerta de Seguimiento de Pedidos</h2>

    <p>Un cliente ha ingresado a la sección "Mis Compras" y ha visualizado sus pedidos exitosamente.</p>

    <ul>
        <li><strong>Celular utilizado:</strong> {{ $celular }}</li>
        <li><strong>Nombre detectado:</strong> {{ $nombreCliente }}</li>
        <li><strong>Fecha/Hora:</strong> {{ date('d/m/Y H:i:s') }}</li>
    </ul>

    <h3>Pedidos visualizados ({{ $pedidos->count() }}):</h3>
    <ul>
        @foreach($pedidos as $pedido)
            <li>
                <strong>Pedido #{{ $pedido->id }}</strong>
                - ${{ number_format($pedido->total, 0, ',', '.') }}
                - Estado: {{ $pedido->estado_personalizado ?: 'Pendiente' }}
                ({{ $pedido->created_at->format('d/m/Y') }})
            </li>
        @endforeach
    </ul>

    <p><em>Este es un correo automático del sistema Ferrindep.</em></p>

</body>

</html>