<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Notificacao;
use App\Models\NotificacaoLidaOcultada;
use Illuminate\Support\Facades\Auth;

class NotificacaoController extends Controller
{
    // Lista notificações visíveis para o usuário
    public function lista()
    {
        $user = Auth::user();

        $notificacoes = Notificacao::query()
            ->where(function ($q) use ($user) {
                $q->whereNull('target_user_id')
                ->orWhere('target_user_id', $user->id);
            })
            ->where(function ($q) use ($user) {
                $q->whereNotIn('id', function ($q2) use ($user) {
                    $q2->select('notificacao_id')
                    ->from('notificacao_lida_ocultadas')
                    ->where('user_id', $user->id)
                    ->where('ocultada', true);
                });
            })
            ->latest()
            ->get()
            ->map(function ($n) {
                $n->created_at_formatted = $n->created_at->format('d/m/Y H:i'); // ex: 29/10/2025 19:38
                return $n;
            });

        return response()->json($notificacoes);
    }

    // Conta notificações não lidas
    public function count()
    {
        $user = Auth::user();

        $countQuery = Notificacao::query()
            ->where(function ($q) use ($user) {
                $q->whereNull('target_user_id')
                ->orWhere('target_user_id', $user->id);
            })
            ->whereNotIn('id', function ($q2) use ($user) {
                $q2->select('notificacao_id')
                ->from('notificacao_lida_ocultadas')
                ->where('user_id', $user->id)
                ->where('lida', true);
            });

        $count = $countQuery->count();

        // LOG para debug
        /*\Log::info('Contagem de notificações não lidas', [
            'user_id' => $user->id,
            'count' => $count,
        ]);*/

        return response()->json(['count' => $count]);
    }


    // Marca todas notificações visíveis como lidas
    public function marcarTodasLidas()
    {
        $user = Auth::user();

        $notificacoes = Notificacao::where(function($q) use ($user) {
                $q->whereNull('target_user_id')
                  ->orWhere('target_user_id', $user->id);
            })
            ->get();

        foreach ($notificacoes as $notificacao) {
            $user->markNotificacaoLida($notificacao->id);
        }

        return response()->json(['success' => true]);
    }

    // Oculta uma notificação específica
    public function ocultar($id)
    {
        $user = Auth::user();
        $user->hideNotificacao($id);

        return response()->json(['success' => true]);
    }
}
