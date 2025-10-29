<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProtocoloMensagem extends Model
{
    use HasFactory;

    protected $table = 'protocolo_mensagens';

    protected $fillable = [
        'protocolo_id',
        'user_id',
        'mensagem',
        'is_staff',
    ];

    public function protocolo()
    {
        return $this->belongsTo(Protocolo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
