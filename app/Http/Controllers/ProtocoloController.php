<?php

namespace App\Http\Controllers;

use App\Models\Protocolo;
use Illuminate\Http\Request;

class ProtocoloController extends Controller
{
    public function create()
    {
        return view('protocolos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email',
            'assunto' => 'required|string',
            'mensagem' => 'required|string',
        ]);

        $validated['user_id'] = auth()->id(); // se estiver logado
        $validated['status'] = 'aberto';
        $validated['numero'] = strtoupper(uniqid('PROTO-')); // número do protocolo

        $protocolo = Protocolo::create($validated);

        return redirect()
            ->route('protocolo.show', $protocolo)
            ->with('success', 'Seu protocolo foi criado com sucesso!');
    }

    public function show(Protocolo $protocolo)
    {
        return view('protocolos.show', compact('protocolo'));
    }
}
