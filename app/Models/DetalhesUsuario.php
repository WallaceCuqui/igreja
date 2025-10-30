<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalhesUsuario extends Model
{
    use HasFactory;

    protected $table = 'detalhes_usuario';

    protected $fillable = [
        'user_id',
        'nome_fantasia',
        'documento',
        'genero',
        'data_nascimento',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'telefone',
        'foto',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
