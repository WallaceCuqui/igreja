<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Protocolo extends Model
{
    use HasFactory;

    protected $fillable = [
        'protocolo',
        'user_id',
        'nome',
        'email',
        'assunto',
        'mensagem',
        'status',
        'atendido_por',
    ];

    // Relacionamentos
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function atendente()
    {
        return $this->belongsTo(User::class, 'atendido_por');
    }

    // Gera número de protocolo automaticamente
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($protocolo) {
            // se for usuário logado, preenche automaticamente
            if (auth()->check()) {
                $protocolo->user_id = auth()->id();
                $protocolo->nome = auth()->user()->name;
                $protocolo->email = auth()->user()->email;
            }

            // gera número tipo 2025-000123
            $next = (self::max('id') ?? 0) + 1;
            $protocolo->protocolo = now()->format('Y') . '-' . str_pad($next, 6, '0', STR_PAD_LEFT);
        });
    }
}
