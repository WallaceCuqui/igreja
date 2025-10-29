<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificacaoLidaOcultada extends Model
{
    protected $table = 'notificacao_lida_ocultadas';

    protected $fillable = [
        'notificacao_id',
        'user_id',
        'lida',
        'ocultada',
        'lida_at',
        'ocultada_at',
    ];

    protected $casts = [
        'lida' => 'boolean',
        'ocultada' => 'boolean',
        'lida_at' => 'datetime',
        'ocultada_at' => 'datetime',
    ];

    public function notificacao(): BelongsTo
    {
        return $this->belongsTo(Notificacao::class, 'notificacao_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /*public static function shouldRegisterNavigation(): bool
    {
        // Exemplo rápido: apenas usuários com permissão 'protocolos.edit' (ajuste conforme seu sistema)
        return auth()->check() && auth()->user()->hasRole('superuser');
    }*/

}
