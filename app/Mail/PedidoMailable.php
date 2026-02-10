<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PedidoMailable extends Mailable
{
    use Queueable, SerializesModels;

    protected $cart;
    protected $pedido;

    // NOTA: Si este archivo espera 2 variables ($cart, $pedido) pero el controlador
    // solo le manda 1, podría fallar. Lo dejo tal cual me lo pasaste para no romper nada,
    // asumiendo que así te está funcionando.
    public function __construct($cart, $pedido)
    {
        $this->cart   = $cart;
        $this->pedido = $pedido;
    }

    public function build()
    {
        // Normalizamos $pedido a array
        if (is_object($this->pedido) && method_exists($this->pedido, 'toArray')) {
            $p = $this->pedido->toArray();
        } else {
            $p = (array) $this->pedido;
        }

        // Intentamos obtener el número de pedido
        $num = null;
        foreach (['numero','n_pedido','nro','nro_pedido','order_number','orden','codigo','id'] as $k) {
            if (isset($p[$k]) && $p[$k] !== '') {
                $num = $p[$k];
                break;
            }
        }

        // --- BLOQUE ELIMINADO ---
        // Aquí estaba el código que agregaba los ceros (str_pad).
        // Al quitarlo, el número se muestra tal cual (ej: 2953).
        // ------------------------

        // Intentamos obtener el nombre del cliente
        $cliente = null;
        foreach (['cliente','cliente_nombre','nombre_cliente','nombre','name','buyer_name','usuario_nombre'] as $k) {
            if (isset($p[$k]) && trim((string)$p[$k]) !== '') {
                $cliente = trim((string)$p[$k]);
                break;
            }
        }

        // Armamos el asunto
        $partNum = $num ? " #{$num}" : '';
        $partCli = $cliente ? " · {$cliente}" : '';
        
        // Asunto limpio sin ceros extra
        $asunto  = "Orden de compra{$partNum}{$partCli} - Ferrindep";

        return $this->subject($asunto)
                    ->view('emails.pedido', [
                        'cart'   => $this->cart,
                        'pedido' => $this->pedido,
                    ]);
    }
}