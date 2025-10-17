<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Notifications\QueuedVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Boot method para observar eventos do Eloquent
     */
    protected static function booted()
    {
        static::created(function ($user) {
            // Só envia e-mail se o usuário não tiver email verificado
            if (!$user->hasVerifiedEmail()) {
                $user->notify((new VerifyEmail())->queue());
            }
        });
    }

    /**
     * Sobrescreve o envio de e-mail de verificação para usar fila.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new QueuedVerifyEmail);
    }
}
