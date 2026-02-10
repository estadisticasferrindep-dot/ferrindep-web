<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

    <p>Nombre: {{$info['nombre']}}</p>
    <p>Teléfono: {{$info['telefono']}}</p>
    <p>Email: {{$info['email']}}</p>
    <p>Empresa: {{$info['empresa']}}</p>


    <p>Tipo de operación:</p>
    <ul>
        @if (array_key_exists('venta',$info))
            <li>Venta</li>
        @endif

        @if (array_key_exists('alquiler',$info)) 
            <li>Alquiler</li>
        @endif
    </ul>

    <br>

    <p>Mensaje: </p>
    <p>{{$info['mensaje']}}</p>

    <br>
</body>
</html>