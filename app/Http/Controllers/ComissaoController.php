<?php

namespace App\Http\Controllers;

use App\Models\Comissao;
use App\Models\Ministerio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class ComissaoController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // 🔒 Verifica se é igreja
        if (! $user || ! $user->isIgreja()) {
            abort(403, 'Apenas igrejas podem acessar as comissões.');
        }

        Log::info('🧭 [ComissaoController@index] Acessando lista de comissões.', [
            'igreja_id' => $user->id,
        ]);

        // Mostra apenas comissões pertencentes aos ministérios da igreja logada
        $comissoes = Comissao::whereHas('ministerio', function ($q) use ($user) {
            $q->where('igreja_id', $user->id);
        })
            ->with(['ministerio', 'membro'])
            ->orderByDesc('data_entrada')
            ->get();

        $ministerio = Ministerio::where('igreja_id', $user->id)->orderBy('nome')->first();
        $membros = User::orderBy('name')->get();

        $editando = null;
        if ($request->filled('edit') && is_numeric($request->get('edit'))) {
            $editando = Comissao::find($request->get('edit'));

            // ⚠️ Só pode editar se for da igreja logada
            if (! $editando || $editando->ministerio->igreja_id !== $user->id) {
                Log::warning('🚫 [ComissaoController@index] Tentativa de edição não autorizada.', [
                    'user_id' => $user->id,
                    'comissao_id' => $request->get('edit'),
                ]);
                abort(403, 'Você não tem permissão para editar esta comissão.');
            }

            Log::info('✏️ [ComissaoController@index] Editando comissão.', [
                'id' => $editando->id,
                'ministerio_id' => $editando->ministerio_id,
            ]);
        }

        return view('ministerios.comissoes', compact(
            'ministerio',
            'comissoes',
            'membros',
            'editando'
        ));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        Log::info('📝 [ComissaoController@store] Iniciando cadastro de comissão.', [
            'user_id' => $user?->id,
            'dados_recebidos' => $request->all(),
        ]);

        // 🔐 Apenas igrejas podem cadastrar
        if (! $user || ! $user->isIgreja()) {
            Log::warning('🚫 [ComissaoController@store] Tentativa de cadastro por usuário não autorizado.');
            return back()->withErrors('Apenas igrejas podem cadastrar comissões.');
        }

        try {
            $validated = $request->validate([
                'ministerio_id' => 'required|exists:ministerios,id',
                'membro_id' => 'required|exists:users,id',
                'funcao' => 'required|string|max:255',
                'observacoes' => 'nullable|string',
                'data_entrada' => 'nullable|date',
                'data_saida' => 'nullable|date|after_or_equal:data_entrada',
                'ativo' => 'nullable|boolean',
            ]);

            $validated['ativo'] = $request->has('ativo');

            // 🚨 Verifica se o ministério pertence à igreja logada
            $ministerio = Ministerio::find($validated['ministerio_id']);
            if (! $ministerio || $ministerio->igreja_id !== $user->id) {
                Log::warning('🚫 [ComissaoController@store] Tentativa de vincular comissão a ministério de outra igreja.', [
                    'user_id' => $user->id,
                    'ministerio_id' => $validated['ministerio_id'],
                ]);
                return back()->withErrors('Você não pode cadastrar comissões em ministérios de outra igreja.');
            }

            $comissao = Comissao::create($validated);

            Log::info('✅ [ComissaoController@store] Comissão criada com sucesso.', [
                'comissao_id' => $comissao->id,
                'ministerio_id' => $comissao->ministerio_id,
            ]);

            return redirect()->route('ministerios.comissoes.index')
                ->with('success', 'Comissão cadastrada com sucesso!');

        } catch (Exception $e) {
            Log::error('❌ [ComissaoController@store] Erro ao cadastrar comissão.', [
                'mensagem' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withErrors('Erro ao salvar comissão.');
        }
    }

    public function update(Request $request, Comissao $comissao)
    {
        $user = auth()->user();

        // 🔒 Verifica se pertence à igreja logada
        if ($comissao->ministerio->igreja_id !== $user->id) {
            Log::warning('🚫 [ComissaoController@update] Tentativa de edição não autorizada.', [
                'user_id' => $user->id,
                'comissao_id' => $comissao->id,
            ]);
            abort(403, 'Você não tem permissão para editar esta comissão.');
        }

        $validated = $request->validate([
            'ministerio_id' => 'required|exists:ministerios,id',
            'membro_id' => 'required|exists:users,id',
            'funcao' => 'required|string|max:255',
            'observacoes' => 'nullable|string',
            'data_entrada' => 'nullable|date',
            'data_saida' => 'nullable|date|after_or_equal:data_entrada',
            'ativo' => 'nullable|boolean',
        ]);

        $validated['ativo'] = $request->has('ativo');

        $comissao->update($validated);

        Log::info('🛠️ [ComissaoController@update] Comissão atualizada com sucesso.', [
            'id' => $comissao->id,
        ]);

        return redirect()->route('ministerios.comissoes.index')
            ->with('success', 'Comissão atualizada com sucesso!');
    }

    public function destroy(Comissao $comissao)
    {
        $user = auth()->user();

        // 🔒 Só pode deletar se for da mesma igreja
        if ($comissao->ministerio->igreja_id !== $user->id) {
            Log::warning('🚫 [ComissaoController@destroy] Tentativa de exclusão não autorizada.', [
                'user_id' => $user->id,
                'comissao_id' => $comissao->id,
            ]);
            abort(403, 'Você não tem permissão para excluir esta comissão.');
        }

        try {
            $comissao->delete();

            Log::info('🗑️ [ComissaoController@destroy] Comissão excluída com sucesso.', [
                'id' => $comissao->id,
            ]);

            return redirect()->route('ministerios.comissoes.index')
                ->with('success', 'Comissão excluída com sucesso!');

        } catch (Exception $e) {
            Log::error('❌ [ComissaoController@destroy] Erro ao excluir comissão.', [
                'mensagem' => $e->getMessage(),
            ]);

            return back()->withErrors('Erro ao excluir comissão.');
        }
    }
}
