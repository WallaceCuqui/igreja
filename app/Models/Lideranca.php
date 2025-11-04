<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lideranca extends Model
{
    use HasFactory;

    protected $fillable = [
        'ministerio_id',
        'lider_id',
        'vice_id',
        'data_inicio',
        'data_fim',
        'status',
    ];

    /** 🔗 Relações **/

    public function ministerio()
    {
        return $this->belongsTo(Ministerio::class);
    }

    public function lider()
    {
        return $this->belongsTo(User::class, 'lider_id');
    }

    public function vice()
    {
        return $this->belongsTo(User::class, 'vice_id');
    }
}
