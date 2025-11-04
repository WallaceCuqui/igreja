<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ministerio extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'data_fundacao',
        'ativo',
        'igreja_id',
    ];

    /** 🔗 Relações **/

    // A igreja (usuário dono do ministério)
    public function igreja()
    {
        return $this->belongsTo(User::class, 'igreja_id');
    }

    // Lideranças
    public function liderancas()
    {
        return $this->hasMany(Lideranca::class);
    }

    // Comissão
    public function comissoes()
    {
        return $this->hasMany(Comissao::class);
    }

    // Integrantes
    public function integrantes()
    {
        return $this->hasMany(IntegranteMinisterio::class);
    }

    // Agenda
    public function agendas()
    {
        return $this->hasMany(Agenda::class);
    }
}
