<?php

namespace App\Http\Controllers;

use App\Models\User;

class MembroPerfilController extends Controller
{
    public function show(User $membro)
    {
        $membro->load([
            'detalhesUsuario',
            'igreja',
            'ministerios',
            'relacoes',
            'relacionamentos.parente',
            'comissoes'
        ]);

        return view('profile.membros.perfil', compact('membro'));
    }
}
