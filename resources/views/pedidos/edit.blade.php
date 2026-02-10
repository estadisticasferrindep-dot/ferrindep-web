@extends('layouts.app')

@section('title','Pedido')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <a style="color:grey;" href="{{ route('pedidos.index') }}">
                <i class="fas fa-arrow-circle-left" style="color:grey; margin-right:6px;"></i>
                Volver al listado de pedidos
            </a>

            <div class="card" style="margin-top:15px;">
                <div class="card-body">

                    @php
                        // C¨®digo / n¨²mero de pedido (mismas alternativas que en el listado)
                        $codigoPedido = $pedido->codigo_pedido
                            ?? $pedido->codigo
                            ?? $pedido->order_code
                            ?? $pedido->numero
                            ?? $pedido->uuid
                            ?? ('FD-' . str_pad((string)($pedido->id ?? 0), 6, '0', STR_PAD_LEFT));

                        // Total formateado para reusar abajo
                        $totalFormateado = '$' . number_format((float)($pedido->total ?? 0), 0, ',', '.');
                    @endphp

                    {{-- T¨ªtulo grande con el c¨®digo de pedido --}}
                    <h1 style="font-size:28px; font-weight:700; margin:5px 0 18px 0;">
                        Pedido
                        <span style="font-family:ui-monospace, SFMono-Regular, Menlo, Consolas, 'Liberation Mono', monospace; font-weight:700;">
                            {{ $codigoPedido }}
                        </span>
                    </h1>

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p>Fecha: {{ optional($pedido->created_at)->format('Y-m-d H:i:s') }}</p>
                    <p>Nombre: {{ $pedido->usuario_nombre }}</p>
                    <p>Monto total: {{ $totalFormateado }}</p>
                    <p>DNI: {{ $pedido->dni }}</p>
                    <p>Email: {{ $pedido->email }}</p>
                    <p>C&oacute;digo Postal: {{ $pedido->cp }}</p>
                    <p>Celular: {{ $pedido->celular }}</p>
                    <p>Localidad: {{ $pedido->localidad }}</p>
                    <p>Direcci&oacute;n: {{ $pedido->direccion }}</p>
                    <p>Provincia: {{ $pedido->provincia }}</p>
                    <p>Forma de pago: {{ $pedido->pago }}</p>
                    <p>Forma de env&iacute;o: {{ $pedido->envio }}</p>
                    <p>Mensaje: {{ $pedido->mensaje }}</p>

                    <br><br>
                    <p>Pedido:</p>

                    @php
                        $items = $pedido->itemsPedidos ?? [];
                    @endphp

                    @if (!empty($items) && count($items))
                        <ol class="mb-0">
                            @foreach ($items as $item)
                                @if($item->con_nombre)
                                    <li style="list-style:none; font-weight:700;">
                                        <strong>{{ $item->cantidad }}X</strong>
                                        {{ $item->nombre }}
                                        @if($item->ancho !== null && $item->ancho !== '')
                                            &mdash; Ancho: {{ $item->ancho }} cm
                                        @endif
                                        @if(isset($item->metros))
                                            ({{ number_format((float)$item->metros, 2, '.', '') }}m)
                                        @endif
                                    </li>
                                @else
                                    <li style="list-style:none; font-weight:700;">
                                        <strong>{{ $item->cantidad }}X</strong>
                                        {{ $item->medidas }} {{ $item->espesor }}
                                        {{ (intval($item->nombre) >= 100 ? $item->nombre/100 . ' m alto/ancho' : $item->nombre . ' cm alto/ancho') }}
                                        @if($item->ancho !== null && $item->ancho !== '')
                                            &mdash; Ancho: {{ $item->ancho }} cm
                                        @endif
                                        @if(isset($item->metros))
                                            ({{ number_format((float)$item->metros, 2, '.', '') }}m)
                                        @endif
                                    </li>
                                @endif
                            @endforeach
                        </ol>
                    @endif

                    {{-- Bloque TOTAL abajo con separador y tipograf¨ªa m¨¢s grande --}}
                    <div style="border-top:1px solid #e5e7eb; margin-top:14px; padding-top:12px; display:flex; justify-content:flex-end;">
                        <div style="font-size:20px; font-weight:700;">
                            TOTAL:
                            <span style="font-family:ui-monospace, SFMono-Regular, Menlo, Consolas, 'Liberation Mono', monospace; font-weight:700;">
                                {{ $totalFormateado }}
                            </span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
