<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Registramos los comandos de la aplicación manualmente
     */
    protected $commands = [
        \App\Console\Commands\EnviarReporteDiario::class, // <--- AQUÍ ESTÁ LA MAGIA
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // Programamos el reporte a las 17:00 (luego cámbialo a 23:00)
        $schedule->command('reporte:busquedas')->dailyAt('23:30');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}