<?php

namespace App\Models;

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

    // Nofificações lidas/ocultadas pelo usuário
    public function notificacoesLidas()
    {
        return $this->hasMany(\App\Models\NotificacaoLidaOcultada::class, 'user_id');
    }

    public function markNotificacaoLida(int $notificacaoId)
    {
        return \App\Models\NotificacaoLidaOcultada::updateOrCreate(
            ['notificacao_id' => $notificacaoId, 'user_id' => $this->id],
            ['lida' => true, 'lida_at' => now()]
        );
    }

    public function hideNotificacao(int $notificacaoId)
    {
        return \App\Models\NotificacaoLidaOcultada::updateOrCreate(
            ['notificacao_id' => $notificacaoId, 'user_id' => $this->id],
            ['ocultada' => true, 'ocultada_at' => now()]
        );
    }


}
