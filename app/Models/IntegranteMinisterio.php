<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Notificacao;
use Illuminate\Support\Facades\Log;

class IntegranteMinisterio extends Model
{
    use HasFactory;

    protected $table = 'integrante_ministerio';

    protected $fillable = [
        'ministerio_id',
        'membro_id',
        'status',
        'data_entrada',
        'data_saida',
        'observacoes',
    ];

    /** 🔗 Relações **/
    public function ministerio()
    {
        return $this->belongsTo(Ministerio::class);
    }

    public function membro()
    {
        return $this->belongsTo(User::class, 'membro_id');
    }

    /** ⚙️ Eventos automáticos **/
    protected static function booted()
    {
        static::created(function ($integrante) {
            if ($integrante->status === 'ativo') {
                self::enviarNotificacaoBoasVindas($integrante);
            }

            if ($integrante->status === 'pendente') {
                self::enviarNotificacaoSolicitacao($integrante);
            }
        });

        static::updated(function ($integrante) {
            if ($integrante->isDirty('status') && $integrante->status === 'ativo') {
                self::enviarNotificacaoBoasVindas($integrante);
            }
        });
    }

    /** 📨 1. Notificação de boas-vindas ao membro **/
    protected static function enviarNotificacaoBoasVindas($integrante)
    {
        try {
            $ministerio = $integrante->ministerio;
            $membro = $integrante->membro;

            if ($ministerio && $membro) {
                Notificacao::create([
                    'titulo' => 'Seja bem-vindo(a)!',
                    'mensagem' => "{$membro->name}, estamos felizes por ter você no ministério {$ministerio->nome}. Seja bem-vindo(a)!",
                    'ministerio_id' => null,
                    'target_user_id' => $membro->id,
                    'created_by' => $ministerio->igreja_id ?? null,
                ]);

                /*Log::info('📨 Notificação de boas-vindas enviada', [
                    'membro_id' => $membro->id,
                    'ministerio_id' => $ministerio->id,
                ]);*/
            }
        } catch (\Throwable $e) {
            Log::error('❌ Erro ao criar notificação de boas-vindas', [
                'erro' => $e->getMessage(),
            ]);
        }
    }

    /** 📨 2. Notificação de solicitação pendente para a igreja **/
    protected static function enviarNotificacaoSolicitacao($integrante)
    {
        try {
            $ministerio = $integrante->ministerio;
            $membro = $integrante->membro;

            if ($ministerio && $membro && $ministerio->igreja_id) {
                Notificacao::create([
                    'titulo' => 'Nova solicitação de ingresso',
                    'mensagem' => "{$membro->name} solicitou ingresso no ministério {$ministerio->nome}.",
                    'ministerio_id' => $ministerio->id,
                    'target_user_id' => $ministerio->igreja_id, // 👈 notifica a igreja
                    'created_by' => $membro->id,
                ]);

                /*Log::info('📨 Notificação de solicitação enviada para igreja', [
                    'igreja_id' => $ministerio->igreja_id,
                    'membro_id' => $membro->id,
                    'ministerio_id' => $ministerio->id,
                ]);*/
            }
        } catch (\Throwable $e) {
            Log::error('❌ Erro ao criar notificação de solicitação pendente', [
                'erro' => $e->getMessage(),
            ]);
        }
    }
}
