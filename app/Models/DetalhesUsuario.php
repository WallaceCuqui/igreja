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

    public function getDocumentoMascaradoAttribute()
    {
        if (!$this->documento) {
            return null;
        }

        $doc = preg_replace('/\D/', '', $this->documento); // remove não numéricos

        // CPF (11 dígitos)
        if (strlen($doc) === 11) {
            return substr($doc, 0, 3) . '.***.***-' . substr($doc, -2);
        }

        // CNPJ (14 dígitos)
        if (strlen($doc) === 14) {
            return substr($doc, 0, 2) . '.***.***/****-' . substr($doc, -2);
        }

        return 'Documento indisponível';
    }

}
