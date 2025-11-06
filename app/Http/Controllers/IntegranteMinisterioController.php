<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Ministerio;
use App\Models\IntegranteMinisterio;
use Illuminate\Http\Request;


class IntegranteMinisterioController extends Controller
{
    public function index(Ministerio $ministerio, Request $request)
    {
        $user = auth()->user();

        // checagem de permissão básica (opcional)
        if (!$user || ! $user->isIgreja() || $ministerio->igreja_id !== $user->id) {
            abort(403);
        }

        // todos os membros candidatos (ajuste o escopo conforme seu domínio)
        $membros = User::where('igreja_id', $user->id)
            ->orderBy('name')
            ->get();

        // integrantes atuais deste ministério (exemplo: relacionamento many-to-many)
        // ajuste conforme sua relação: $ministerio->integrantes()->pluck('user_id')->toArray()
        $integrantes = $ministerio->integrantes()->orderBy('name')->get(); // collection de Users com pivot
        $integrantesAtuais = $integrantes->pluck('id')->toArray();

        $vinculosAtuais = $integrantes->mapWithKeys(function ($item) {
            return [$item->id => ($item->pivot->tipo_vinculo ?? null)];
        })->toArray();


        return view('ministerios.integrantes', compact(
            'ministerio',
            'membros',
            'integrantesAtuais',
            'vinculosAtuais'
        ));

    }

    public function store(Request $request, Ministerio $ministerio)
    {
        $user = auth()->user();

        if (!$user->isIgreja() || $ministerio->igreja_id !== $user->id) {
            abort(403, 'Acesso negado.');
        }

        $integrantes = $request->input('membros', []);
        $ministerio->integrantes()->sync($integrantes);

        Log::info('✅ [IntegranteMinisterioController@store] Integrantes atualizados com sucesso', [
            'ministerio_id' => $ministerio->id,
            'total' => count($integrantes),
        ]);

        return redirect()
            ->route('ministerios.integrantes', $ministerio->id)
            ->with('success', 'Integrantes atualizados com sucesso!');
    }

    public function show(Ministerio $ministerio)
    {
        return view('ministerios.show', compact('ministerio'));
    }

    public function update(Request $request, IntegranteMinisterio $integrante)
    {
        $validated = $request->validate([
            'tipo_vinculo' => 'sometimes|string',
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
}
