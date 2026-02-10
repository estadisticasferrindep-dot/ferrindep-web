<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactanosMailable extends Mailable
{
    use Queueable, SerializesModels;

    protected $info;
    protected $archivo;
    public $subject = "Información de contacto";
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($info, $archivo= false)
    {
        // aca recibo la info 
        $this->info=$info;
        $this->archivo=$archivo;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $main= $this->view('emails.contactanos', ['info'=>$this->info]);

        if ($this->archivo) {
            $main = $main->attachFromStorage($this->archivo);
        }
        return $main;
    }
}
