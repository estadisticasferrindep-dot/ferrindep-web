<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>



    <h3>Registro de Usuario</h3>
    <p>Email: {{$info['email']}}</p>
    

    {!!$configpedido->mail_registro!!}
    
{{-- 
    <p>Motivo/s de consulta:</p>
    <ul>
        @if (array_key_exists('reparaciones',$info))
            <li>Reparaciones</li>
        @endif

        @if (array_key_exists('repuestos_y_accesorios',$info)) 
            <li>Repuestos y accesorios</li>
        @endif

        @if (array_key_exists('pintura',$info)) 
            <li>Pintura</li>
        @endif

        @if (array_key_exists('capacitaciones',$info)) 
            <li>Capacitaciones</li>
        @endif

    </ul>

    <br>

    <p>Mensaje: </p>
    <p>{{$info['mensaje']}}</p> --}}

    <br>
</body>
</html>