<?php

namespace App\Mail;

use App\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PedidoListo extends Mailable
{
    use Queueable, SerializesModels;

    public $pedido;
    public $cart;

    public function __construct(Pedido $pedido, $cart)
    {
        $this->pedido = $pedido;
        $this->cart = $cart;
    }

    public function build()
    {
        $nombre = $this->pedido->usuario_nombre ?? $this->pedido->nombre;
        return $this->view('emails.pedido_listo')
            ->subject('PEDIDO #' . $this->pedido->id . ' LISTO PARA RETIRAR')
            ->with([
                'nombre' => $nombre,
                'pedido' => $this->pedido,
                'cart' => $this->cart
            ]);
    }
}
