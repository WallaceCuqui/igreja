<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ministerio;
use App\Models\IntegranteMinisterio;
use Illuminate\Http\Request;

class IntegranteMinisterioController extends Controller
{
    public function index(Ministerio $ministerio, Request $request)
    {
        $user = auth()->user();

        // Apenas a igreja dona do ministério pode gerenciar
        if (! $user || ! $user->isIgreja() || $ministerio->igreja_id !== $user->id) {
            abort(403);
        }

        // Pega somente os integrantes vinculados a este ministério
        $integrantes = $ministerio->integrantes()
            ->orderBy('name')
            ->get();

        // Mapeia status atuais
        $statusAtuais = $integrantes->mapWithKeys(function ($item) {
            return [$item->id => $item->pivot->status];
        })->toArray();

        return view('ministerios.integrantes', compact(
            'ministerio',
            'integrantes',
            'statusAtuais'
        ));
    }


    public function solicitarEntrada(Ministerio $ministerio)
    {
        $user = auth()->user();

        $existe = IntegranteMinisterio::where('ministerio_id', $ministerio->id)
            ->where('membro_id', $user->id)
            ->whereNull('data_saida')
            ->exists();

        if ($existe) {
            return back()->withErrors('Você já está vinculado a este ministério.');
        }

        $status = $ministerio->politica_ingresso === 'restrito' ? 'pendente' : 'ativo';

        IntegranteMinisterio::create([
            'ministerio_id' => $ministerio->id,
            'membro_id' => $user->id,
            'status' => $status,
            'data_entrada' => now(),
        ]);

        $mensagem = $status === 'pendente'
            ? 'Solicitação enviada com sucesso! Aguarde aprovação da liderança.'
            : 'Inscrição confirmada! Bem-vindo ao ministério.';

        return back()->with('success', $mensagem);
    }


    public function ativar(Ministerio $ministerio, User $membro)
    {
        $user = auth()->user();

        if (! $user->isIgreja() || $ministerio->igreja_id !== $user->id) {
            abort(403);
        }

        $integrante = IntegranteMinisterio::where('ministerio_id', $ministerio->id)
            ->where('membro_id', $membro->id)
            ->first();

        if (! $integrante) {
            $integrante = IntegranteMinisterio::create([
                'ministerio_id' => $ministerio->id,
                'membro_id' => $membro->id,
                'status' => 'ativo',
                'data_entrada' => now(),
            ]);
        } else {
            $integrante->update([
                'status' => 'ativo',
                'data_saida' => null,
            ]);
        }

        return response()->json(['status' => 'ativo', 'id' => $integrante->id]);
    }

    public function remover(Ministerio $ministerio, User $membro)
    {
        $user = auth()->user();

        if (! $user->isIgreja() || $ministerio->igreja_id !== $user->id) {
            abort(403);
        }

        $integrante = IntegranteMinisterio::where('ministerio_id', $ministerio->id)
            ->where('membro_id', $membro->id)
            ->first();

        if ($integrante) {
            $integrante->delete();
        }

        return response()->json(['status' => 'removido']);
    }

    public function update(Request $request, IntegranteMinisterio $integrante)
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:pendente,ativo,inativo',
            'data_saida' => 'nullable|date',
            'observacoes' => 'nullable|string',
        ]);

        $integrante->update($validated);

        return response()->json($integrante);
    }

    public function destroy(IntegranteMinisterio $integrante)
    {
        $integrante->delete();

        return response()->json(null, 204);
    }

    public function show(Ministerio $ministerio)
    {
        return view('ministerios.show', compact('ministerio'));
    }
}
