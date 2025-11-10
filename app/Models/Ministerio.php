<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ministerio extends Model
{
    use HasFactory;

    protected $fillable = [
        'igreja_id',
        'nome',
        'descricao',
        'data_fundacao',
        'ativo',
        'lider_id',
        'vice_id',
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

    public function lider()
    {
        return $this->belongsTo(User::class, 'lider_id');
    }

    public function vice()
    {
        return $this->belongsTo(User::class, 'vice_id');
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
            User::class,
            'integrante_ministerio',
            'ministerio_id',
            'membro_id'
        )->withPivot('data_entrada', 'data_saida', 'observacoes')
        ->withTimestamps();
    }


    // Agenda
    public function agendas()
    {
        return $this->hasMany(Agenda::class);
    }
}
