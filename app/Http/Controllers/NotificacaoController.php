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

        // Pegamos a query montada pelo usuário (sem executar ainda)
        $query = $user->notificacoesVisiveis();

        // Log do SQL e bindings vindo do query builder
        /*try {
            Log::info('NotificacaoController@lista - antes get', [
                'user_id' => $user->id,
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings(),
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao logar query em NotificacaoController@lista: '.$e->getMessage());
        }*/

        // Executa a query
        $notificacoes = $query
            ->get()
            ->map(function ($n) {
                $n->created_at_formatted = $n->created_at->format('d/m/Y H:i');
                return $n;
            });

        // Log do resultado (contagem e exemplos)
        /*Log::info('NotificacaoController@lista - resultado', [
            'user_id' => $user->id,
            'count' => $notificacoes->count(),
            'first_ids' => $notificacoes->pluck('id')->take(10)->values()->all(),
            // mostra alguns atributos úteis para debug
            'sample' => $notificacoes->take(5)->map(function($n) {
                return [
                    'id' => $n->id,
                    'titulo' => $n->titulo,
                    'ministerio_id' => $n->ministerio_id,
                    'target_user_id' => $n->target_user_id,
                ];
            })->values()->all(),
        ]);*/

        return response()->json($notificacoes);
    }       




    // Conta notificações não lidas
    public function count()
    {
        $user = Auth::user();

        $count = $user->notificacoesVisiveis()
            ->whereNotIn('id', function ($q) use ($user) {
                $q->select('notificacao_id')
                ->from('notificacao_lida_ocultadas')
                ->where('user_id', $user->id)
                ->where('lida', true);
            })
            ->count();

        return response()->json(['count' => $count]);
    }



    // Marca todas notificações visíveis como lidas
    public function marcarTodasLidas()
    {
        $user = Auth::user();

        $notificacoes = $user->notificacoesVisiveis()->get();

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

    public static function criarNotificacaoMinisterio(
        ?int $ministerioId,
        string $titulo,
        ?string $mensagem = null,
        ?int $createdBy = null
    ): Notificacao {
        $notificacao = Notificacao::create([
            'titulo' => $titulo,
            'mensagem' => $mensagem,
            'ministerio_id' => $ministerioId, // pode ser null
            'target_user_id' => null,         // broadcast (todos do ministério)
            'created_by' => $createdBy ?? auth()->id(),
            'starts_at' => now(),
        ]);

        /*\Log::info('✅ Notificação criada', [
            'ministerio_id' => $ministerioId,
            'titulo' => $titulo,
            'created_by' => $createdBy ?? auth()->id(),
        ]);*/

        return $notificacao;
    }


}
