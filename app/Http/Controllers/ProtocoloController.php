<?php

namespace App\Http\Controllers;

use App\Models\Protocolo;
use App\Models\ProtocoloMensagem;
use Illuminate\Http\Request;

class ProtocoloController extends Controller
{

    public function create()
    {
        $user = auth()->user();

        $protocolos = Protocolo::where('user_id', $user->id)
            ->latest()
            ->get();

        return view('protocolos.create', [
            'user' => $user,
            'email' => $user?->email,
            'protocolos' => $protocolos,
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

        $data = [
            'user_id' => auth()->id(),
            'nome' => $validated['nome'],
            'email' => $validated['email'],
            'assunto' => $validated['assunto'],
            'status' => 'aberto',
            // certifique-se do nome correto da coluna: 'protocolo' na migration
            'protocolo' => strtoupper(uniqid('PROTO-')),
        ];

        \DB::beginTransaction();
        try {
            $protocolo = Protocolo::create($data);

            // cria a mensagem inicial na tabela de mensagens
            ProtocoloMensagem::create([
                'protocolo_id' => $protocolo->id,
                'user_id' => auth()->id(),
                'mensagem' => $validated['mensagem'],
                'is_staff' => false,
            ]);

            \DB::commit();

            return redirect()
                ->route('protocolo.show', $protocolo)
                ->with('success', 'Seu protocolo foi criado com sucesso!');
        } catch (\Throwable $e) {
            \DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Erro ao criar protocolo: ' . $e->getMessage()]);
        }
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
