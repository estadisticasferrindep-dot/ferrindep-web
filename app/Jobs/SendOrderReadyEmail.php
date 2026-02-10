<?php

namespace App\Jobs;

use App\Models\Pedido;
use App\Mail\PedidoListo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOrderReadyEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $pedido;

    public function __construct(Pedido $pedido)
    {
        $this->pedido = $pedido;
    }

    public function handle()
    {
        $this->pedido->load('itemsPedidos');
        $cart = $this->pedido->itemsPedidos;

        if ($this->pedido->email) {
            Mail::to($this->pedido->email)
                ->bcc(['estadisticasferrindep@gmail.com', 'ferrindepventas@gmail.com'])
                ->send(new PedidoListo($this->pedido, $cart));
        }
    }
}
