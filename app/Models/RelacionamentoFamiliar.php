<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelacionamentoFamiliar extends Model
{
    protected $table = 'relacionamentos_familiares';
    
    protected $fillable = ['user_id', 'parente_id', 'tipo'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parente()
    {
        return $this->belongsTo(User::class, 'parente_id');
    }
}

