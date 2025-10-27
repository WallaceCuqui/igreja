<?php

namespace App\Http\Controllers;

use App\Models\Protocolo;
use App\Models\ProtocoloMensagem;
use Illuminate\Http\Request;

class ProtocoloController extends Controller
{
    public function create()
    {
        return view('protocolos.create', [
            'user' => auth()->user(),
            'email' => auth()->user()?->email,
        ]);
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

    

    public function responder(Request $request, Protocolo $protocolo)
    {
        $validated = $request->validate([
            'mensagem' => 'required|string',
        ]);

        ProtocoloMensagem::create([
            'protocolo_id' => $protocolo->id,
            'user_id' => auth()->id(),
            'mensagem' => $validated['mensagem'],
            'is_staff' => auth()->check(), // true se for staff logado
        ]);

        return redirect()->back()->with('success', 'Mensagem enviada com sucesso!');
    }

}
