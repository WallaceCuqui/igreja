<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use App\Models\NotificacaoLidaOcultada;
use App\Models\Notificacao;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\QueuedVerifyEmail;
use Illuminate\Database\Eloquent\Builder;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'type', 
        'igreja_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Boot method para observar eventos do Eloquent
     */
    protected static function booted()
    {
        static::created(function ($user) {
            //
        });
    }

    // Associação com DetalhesUsuario
    public function detalhesUsuario()
    {
        return $this->hasOne(DetalhesUsuario::class, 'user_id');
    }

    // 👉 Igreja a que o membro pertence
    public function igreja()
    {
        return $this->belongsTo(User::class, 'igreja_id');
    }

    // 👉 Membros da igreja
    public function membros()
    {
        return $this->hasMany(User::class, 'igreja_id')->where('type', 'membro');
    }

    public function isIgreja()
    {
        return $this->type === 'igreja';
    }

    public function isMembro()
    {
        return $this->type === 'membro';
    }

    // Relacaos (dependentes sem login)
    public function relacoes()
    {
        return $this->hasMany(Relacao::class, 'membro_id');
    }

    // Relacionamentos familiares
    public function relacionamentos()
    {
        return $this->hasMany(RelacionamentoFamiliar::class, 'user_id');
    }

    public function ministerios()
    {
        return $this->belongsToMany(Ministerio::class, 'integrante_ministerio', 'membro_id', 'ministerio_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function notificacoesVisiveis()
{
    // IDs dos ministérios do usuário (array simples)
    $ministeriosIds = $this->ministerios()->pluck('ministerios.id')->filter()->all();

    // IDs de notificações ocultadas pelo usuário
    $notificacoesOcultadas = $this->notificacoesLidas()
        ->where('ocultada', true)
        ->pluck('notificacao_id')
        ->toArray();

    // Monta a query (sem executar)
    $query = Notificacao::query()
        ->where(function (Builder $query) use ($ministeriosIds) {
            $query
                ->where(function ($q) {
                    $q->whereNull('target_user_id')
                      ->whereNull('ministerio_id');
                })
                ->orWhere(function ($q) use ($ministeriosIds) {
                    // só adiciona whereIn se houver ministérios
                    if (!empty($ministeriosIds)) {
                        $q->whereNull('target_user_id')
                          ->whereIn('ministerio_id', $ministeriosIds);
                    }
                })
                ->orWhere('target_user_id', $this->id);
        })
        ->whereNotIn('id', $notificacoesOcultadas);

    // LOG: SQL + bindings + variáveis que afetam o filtro
    /*try {
        Log::info('notificacoesVisiveis - query debug', [
            'user_id' => $this->id,
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings(),
            'ministeriosIds' => $ministeriosIds,
            'notificacoesOcultadas' => $notificacoesOcultadas,
        ]);
    } catch (\Exception $e) {
        // em caso de ambiente onde toSql/getBindings causem problema, loga apenas a mensagem
        Log::error('Erro ao gerar debug de notificacoesVisiveis: '.$e->getMessage());
    }*/

    return $query->latest();
}



    // Nofificações lidas/ocultadas pelo usuário
    public function notificacoesLidas()
    {
        return $this->hasMany(NotificacaoLidaOcultada::class, 'user_id');
    }

    public function markNotificacaoLida(int $notificacaoId)
    {
        return NotificacaoLidaOcultada::updateOrCreate(
            ['notificacao_id' => $notificacaoId, 'user_id' => $this->id],
            ['lida' => true, 'lida_at' => now()]
        );
    }

    public function hideNotificacao(int $notificacaoId)
    {
        return NotificacaoLidaOcultada::updateOrCreate(
            ['notificacao_id' => $notificacaoId, 'user_id' => $this->id],
            ['ocultada' => true, 'ocultada_at' => now()]
        );
    }


}
