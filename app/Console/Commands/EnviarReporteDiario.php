<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReporteBusquedasMailable;
use Carbon\Carbon;

class EnviarReporteDiario extends Command
{
    // El nombre clave para ejecutar esto
    protected $signature = 'reporte:busquedas';
    protected $description = 'Envía el reporte diario de búsquedas web';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // 1. Buscamos los datos de HOY
        $hoy = Carbon::today();
        
        $busquedas = DB::table('historial_busquedas')
            ->whereDate('created_at', $hoy)
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Si hay búsquedas, enviamos el email
        if ($busquedas->count() > 0) {
            // PON AQUÍ TU EMAIL REAL ↓↓↓
            Mail::to('estadisticasferrindep@gmail.com')->send(new ReporteBusquedasMailable($busquedas));
            
            $this->info('Reporte enviado con éxito.');
        } else {
            $this->info('No hubo búsquedas hoy, no se envió email.');
        }
    }
}