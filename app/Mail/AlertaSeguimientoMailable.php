<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlertaSeguimientoMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $celular;
    public $pedidos;
    public $nombreCliente;

    public function __construct($celular, $pedidos, $nombreCliente)
    {
        $this->celular = $celular;
        $this->pedidos = $pedidos;
        $this->nombreCliente = $nombreCliente;
    }

    public function build()
    {
        $count = $this->pedidos->count();
        $asunto = "Alerta de Seguimiento: Cliente consultó {$count} pedido(s)";

        return $this->subject($asunto)
            ->view('emails.alerta_seguimiento');
    }
}
