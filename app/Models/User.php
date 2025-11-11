<?php

namespace App\Models;

use App\Models\NotificacaoLidaOcultada;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\QueuedVerifyEmail;


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
