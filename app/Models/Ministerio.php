<?php

namespace App\Models;

use App\Models\User;
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

    public function getLiderAttribute()
    {
        return $this->liderancas()->first()?->lider;
    }

    public function getViceAttribute()
    {
        return $this->liderancas()->first()?->vice;
    }



    // Comissão
    public function comissoes()
    {
        return $this->hasMany(Comissao::class);
    }

    // Integrantes do ministério (users vinculados)
    public function integrantes()
    {
        return $this->belongsToMany(
            User::class,
            'integrante_ministerio', // nome da tabela pivô
            'ministerio_id',         // FK para este model
            'membro_id'              // FK para o model User
        )->withPivot(['status', 'data_entrada', 'data_saida', 'observacoes'])
        ->withTimestamps();
    }




    // Agenda
    public function agendas()
    {
        return $this->hasMany(Agenda::class);
    }
}
