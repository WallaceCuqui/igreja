<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Relacao extends Model
{
    use HasFactory;

    protected $table = 'relacoes';

    protected $fillable = [
        'membro_id',
        'relacionado_id',
        'nome',
        'data_nascimento',
        'sexo',
        'tipo',
        'foto',
    ];

    public function membro()
    {
        return $this->belongsTo(User::class, 'membro_id');
    }

    public function ministerios()
    {
        return $this->belongsToMany(Ministerio::class, 'relacao_ministerio');
    }
}
