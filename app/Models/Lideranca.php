<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Lideranca extends Model
{
    protected $fillable = [
        'ministerio_id',
        'lider_id',
        'vice_id',
        'data_inicio',
        'data_fim',
        'ativo',
    ];

    protected static function booted()
    {
        static::saving(function ($lideranca) {
            $lideranca->sincronizarStatus();
        });
    }

    public function sincronizarStatus()
    {
        $hoje = Carbon::today();

        if ($this->data_inicio && $hoje->lt(Carbon::parse($this->data_inicio))) {
            $this->ativo = false; // ainda não começou
        } elseif ($this->data_fim && $hoje->gt(Carbon::parse($this->data_fim))) {
            $this->ativo = false; // já terminou
        } else {
            $this->ativo = true; // dentro do período
        }
    }


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
