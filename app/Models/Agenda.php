<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'ministerio_id',
        'titulo',
        'descricao',
        'local',
        'data_inicio',
        'data_fim',
        'responsavel_id',
        'status',
        'tipo_evento',
    ];

    /** 🔗 Relações **/

    public function ministerio()
    {
        return $this->belongsTo(Ministerio::class);
    }

    public function responsavel()
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }
}
