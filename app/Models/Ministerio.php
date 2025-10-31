<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ministerio extends Model
{
    protected $fillable = ['nome', 'descricao'];

    public function relacoes()
    {
        return $this->belongsToMany(Relacao::class, 'ministerio_relacao');
    }
}

