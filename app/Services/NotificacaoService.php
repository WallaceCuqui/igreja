<?php

namespace App\Services;

class NotificacaoService
{
    public function getVisiveisFor(User $user)
    {
        return Notificacao::getNotificacoesVisiveisFor($user); // ou reimplemente aqui se preferir
    }

    public function countNaoLidasFor(User $user)
    {
        return Notificacao::countNaoLidas($user);
    }
}
