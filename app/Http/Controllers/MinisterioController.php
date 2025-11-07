<?php

namespace App\Http\Controllers;

use App\Models\Ministerio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class MinisterioController extends Controller
{

    // app/Http/Controllers/MinisterioController.php

    public function show(Ministerio $ministerio)
    {
        // carregar relacionamentos se precisar: membros, liderancas etc.
        // $ministerio->load('integrantes.membro', 'liderancas', 'comissoes');

        return view('ministerios.show', compact('ministerio'));
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // 🔒 Apenas igrejas podem acessar
        if (! $user || ! $user->isIgreja()) {
            abort(403, 'Apenas igrejas podem acessar a lista de ministérios.');
        }

        Log::info('🧭 [MinisterioController@index] Acessando lista de ministérios.', [
            'igreja_id' => $user->id,
        ]);

        // Mostra apenas os ministérios da igreja logada
        $ministerios = Ministerio::where('igreja_id', $user->id)
            ->orderBy('nome')
            ->get();

        // Se não houver ministérios, exibe aviso
        if ($ministerios->isEmpty()) {
            Log::info('ℹ️ [MinisterioController@index] Nenhum ministério encontrado para esta igreja.');
        }

        $editando = null;
        if ($request->has('edit')) {
            $editando = Ministerio::find($request->get('edit'));

            // Segurança extra: só pode editar se for da mesma igreja
            if (! $editando || $editando->igreja_id !== $user->id) {
                Log::warning('🚫 [MinisterioController@index] Tentativa de edição de ministério não autorizado.', [
                    'user_id' => $user->id,
                    'ministerio_id' => $request->get('edit'),
                ]);
                abort(403, 'Você não tem permissão para editar este ministério.');
            }

            Log::info('✏️ [MinisterioController@index] Editando ministério.', [
                'id' => $editando->id,
                'igreja_id' => $editando->igreja_id,
            ]);
        }

        return view('ministerios.cadastro', compact('ministerios', 'editando'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        Log::info('📝 [MinisterioController@store] Iniciando cadastro de ministério.', [
            'user_id' => $user?->id,
            'dados_recebidos' => $request->all(),
        ]);

        // 🔐 Apenas igrejas podem cadastrar
        if (! $user || ! $user->isIgreja()) {
            Log::warning('🚫 [MinisterioController@store] Tentativa de cadastro por usuário não autorizado.');
            return back()->withErrors('Apenas igrejas podem cadastrar ministérios.');
        }

        try {
            $request->merge([
                'ativo' => $request->has('ativo'),
            ]);

            $validated = $request->validate([
                'nome' => 'required|string|max:255',
                'descricao' => 'nullable|string',
                'data_fundacao' => 'nullable|date',
                'ativo' => 'boolean',
                'politica_ingresso' => 'required|in:aberto,restrito',
            ]);


            $validated['igreja_id'] = $user->id;

            $ministerio = Ministerio::create($validated);

            Log::info('🎯 [MinisterioController@store] Ministério criado com sucesso.', [
                'ministerio_id' => $ministerio->id,
                'igreja_id' => $ministerio->igreja_id,
            ]);

            return redirect()->route('ministerios.cadastro')
                ->with('success', 'Ministério cadastrado com sucesso!');

        } catch (Exception $e) {
            Log::error('❌ [MinisterioController@store] Erro ao cadastrar ministério.', [
                'mensagem' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withErrors('Erro ao salvar ministério.');
        }
    }

    public function update(Request $request, Ministerio $ministerio)
    {
        $user = auth()->user();

        // 🔒 Verifica se o ministério pertence à igreja logada
        if ($ministerio->igreja_id !== $user->id) {
            Log::warning('🚫 [MinisterioController@update] Tentativa de edição de ministério não autorizado.', [
                'user_id' => $user->id,
                'ministerio_id' => $ministerio->id,
            ]);
            abort(403, 'Você não tem permissão para editar este ministério.');
        }

        Log::info('🛠️ [MinisterioController@update] Atualizando ministério.', [
            'id' => $ministerio->id,
        ]);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'data_fundacao' => 'nullable|date',
            'ativo' => 'nullable|boolean',
            'politica_ingresso' => 'required|in:aberto,restrito',
        ]);

        $validated['ativo'] = $request->has('ativo');

        $ministerio->update($validated);

        Log::info('✅ [MinisterioController@update] Ministério atualizado com sucesso.', [
            'id' => $ministerio->id,
        ]);

        return redirect()->route('ministerios.cadastro')
            ->with('success', 'Ministério atualizado com sucesso!');
    }

    public function destroy(Ministerio $ministerio)
    {
        $user = auth()->user();

        // 🔒 Só pode deletar ministérios da própria igreja
        if ($ministerio->igreja_id !== $user->id) {
            Log::warning('🚫 [MinisterioController@destroy] Tentativa de exclusão não autorizada.', [
                'user_id' => $user->id,
                'ministerio_id' => $ministerio->id,
            ]);
            abort(403, 'Você não tem permissão para excluir este ministério.');
        }

        try {
            $ministerio->delete();

            Log::info('🗑️ [MinisterioController@destroy] Ministério excluído com sucesso.', [
                'id' => $ministerio->id,
            ]);

            return redirect()->route('ministerios.cadastro')
                ->with('success', 'Ministério excluído com sucesso!');

        } catch (Exception $e) {
            Log::error('❌ [MinisterioController@destroy] Erro ao excluir ministério.', [
                'mensagem' => $e->getMessage(),
            ]);

            return back()->withErrors('Erro ao excluir ministério.');
        }
    }
}
