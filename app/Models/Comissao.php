<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comissao extends Model
{
    use HasFactory;

    protected $fillable = [
        'ministerio_id',
        'membro_id',
        'funcao',
        'data_entrada',
        'data_saida',
        'observacoes',
    ];

    /** 🔗 Relações **/

    public function ministerio()
    {
        return $this->belongsTo(Ministerio::class);
    }

    public function membro()
    {
        return $this->belongsTo(User::class, 'membro_id');
    }
}
