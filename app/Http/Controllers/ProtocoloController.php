<?php

namespace App\Http\Controllers;

use App\Models\Protocolo;
use Illuminate\Http\Request;

class ProtocoloController extends Controller
{
    public function create()
    {
        return view('site.protocolo');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required',
            'email' => 'required|email',
            'assunto' => 'required',
            'mensagem' => 'required',
        ]);

        $protocolo = Protocolo::create($validated);

        return redirect()
            ->back()
            ->with('success', "Protocolo criado com sucesso! Número: {$protocolo->protocolo}");
    }
}
