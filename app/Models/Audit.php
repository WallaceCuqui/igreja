<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    protected $fillable = [
        'action', 'model_type', 'model_id', 'user_id',
        'ip', 'url', 'user_agent', 'before', 'after', 'changes'
    ];

    protected $casts = [
        'before' => 'array',
        'after' => 'array',
        'changes' => 'array',
    ];
}