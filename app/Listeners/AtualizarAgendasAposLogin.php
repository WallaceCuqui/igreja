<?php

// app/Listeners/AtualizarAgendasAposLogin.php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\Agenda;
use Illuminate\Support\Facades\Log;

class AtualizarAgendasAposLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        // Só atualiza se o usuário for de uma igreja (ou membro vinculado)
        if ($user->isIgreja()) {
            // Atualiza agendas da própria igreja
            $agendas = Agenda::whereHas('ministerio', function ($q) use ($user) {
                $q->where('igreja_id', $user->id);
            })
            ->where('status', '!=', 'cancelado')
            ->where(function ($q) {
                $q->whereDate('data_fim', '<', now())
                  ->orWhere(function ($q2) {
                      $q2->whereNull('data_fim')
                         ->whereDate('data_inicio', '<', now());
                  });
            })
            ->where('status', '!=', 'realizado')
            ->update(['status' => 'realizado']);

            Log::info('✅ Agendas atualizadas automaticamente no login da igreja', [
                'igreja_id' => $user->id,
            ]);
        }
    }
}
