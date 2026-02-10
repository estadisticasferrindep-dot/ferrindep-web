<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PostVentaMailable extends Mailable
{
    use Queueable, SerializesModels;

    protected $info;
    public $subject = "Servicios Post Venta";
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($info)
    {
        // aca recibo la info 
        $this->info=$info;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.contactanos_post_venta', ['info'=>$this->info]);
    }
}
