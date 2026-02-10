<!DOCTYPE html>
<html>
<head>
    <title>Reporte Diario de Búsquedas</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">

    <h2 style="color: #FD914D;">📊 Reporte de Búsquedas - Ferrindep</h2>
    <p>Hola, aquí tienes el resumen de lo que buscaron los clientes hoy en la web.</p>

    <div style="background: #f4f4f4; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
        <strong>Total de búsquedas hoy:</strong> {{ count($busquedas) }}
    </div>

    @if(count($busquedas) > 0)
        <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
            <thead>
                <tr style="background-color: #333; color: #fff;">
                    <th style="padding: 10px; text-align: left;">Hora</th>
                    <th style="padding: 10px; text-align: left;">Familia</th>
                    <th style="padding: 10px; text-align: left;">Ancho</th>
                    <th style="padding: 10px; text-align: left;">Medida</th>
                    <th style="padding: 10px; text-align: left;">Espesor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($busquedas as $b)
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 8px;">{{ \Carbon\Carbon::parse($b->created_at)->format('H:i') }}</td>
                    <td style="padding: 8px;">{{ $b->familia }}</td>
                    <td style="padding: 8px;">{{ $b->ancho ?? '-' }}</td>
                    <td style="padding: 8px;">{{ $b->medida ?? '-' }}</td>
                    <td style="padding: 8px;">{{ $b->espesor ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Hoy no hubo actividad en el buscador.</p>
    @endif

    <p style="margin-top: 30px; font-size: 12px; color: #999;">
        Este reporte se generó automáticamente el {{ date('d/m/Y H:i') }}
    </p>

</body>
</html>