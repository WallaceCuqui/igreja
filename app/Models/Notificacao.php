<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacao extends Model
{
    protected $table = 'notificacoes';

    protected $fillable = [
        'titulo',
        'mensagem',
        'created_by',
        'target_user_id',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    // quem criou (admin)
    public function targetUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'target_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
    
    // pivot lidos/ocultadas
    public function lidos(): HasMany
    {
        return $this->hasMany(NotificacaoLidaOcultada::class, 'notificacao_id');
    }

    // opcional: scope para notificações ativas
    public function scopeAtivas($query)
    {
        $now = now();
        return $query->where(function ($q) use ($now) {
            $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
        });
    }
}
