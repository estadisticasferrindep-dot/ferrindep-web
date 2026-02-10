<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReporteBusquedasMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $busquedas;

    public function __construct($busquedas)
    {
        $this->busquedas = $busquedas;
    }

    public function build()
    {
        return $this->subject('📊 Resumen Diario de Búsquedas Ferrindep')
                    ->view('emails.reporte_busquedas');
    }
}