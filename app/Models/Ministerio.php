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

    protected $casts = [
        'data_fundacao' => 'date', // ou 'datetime' se guardar horário
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
        return $this->belongsToMany(
            \App\Models\User::class,       // ajuste para o model de membro
            'integrante_ministerio',       // nome da tabela pivô
            'ministerio_id',               // fk neste pivot para Ministerio
            'membro_id'                      // fk neste pivot para User (ajuste se diferente)
        )->withPivot('tipo_vinculo')
        ->withTimestamps();
    }

    // Agenda
    public function agendas()
    {
        return $this->hasMany(Agenda::class);
    }
}
