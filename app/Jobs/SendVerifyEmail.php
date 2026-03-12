<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendVerifyEmail implements ShouldQueue
{
    use Queueable;

    private $user;

    /**
     * Create a new job instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Utilizamos el listener nativo de notificaciones que mandará la plantilla base de verificación.
            $this->user->notify(new \Illuminate\Auth\Notifications\VerifyEmail);
        } catch (\Throwable $th) {
            // Atrapamos silenciosamente la caída del Transport SMTP para que el trabajador de la cola 
            // no lo reintegre indefinidamente generando errores bloqueantes.
            \Illuminate\Support\Facades\Log::error('SMTP Transport failed for VerifyEmail: ' . $th->getMessage());
        }
    }
}
