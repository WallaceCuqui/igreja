<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Models\User;

class ProfileController extends Controller
{

    /**
     * Busca dinâmica de membros da igreja autenticada (para autocomplete).
     */
    public function buscaMembro(Request $request)
    {
        $user = auth()->user();

        Log::info('🔍 [ProfileController@buscaMembro] Requisição recebida', [
            'userId' => $user?->id,
            'query' => $request->get('q')
        ]);

        if (!$user || !$user->isIgreja()) {
            Log::warning('🚫 [ProfileController@buscaMembro] Usuário não autorizado ou não é igreja', [
                'userId' => $user?->id
            ]);
            return response()->json([]);
        }

        $query = $request->get('q', '');

        $membros = User::where('igreja_id', $user->id)
            ->where('name', 'like', "%{$query}%")
            ->select('id', 'name')
            ->limit(10)
            ->get();

        Log::info('✅ [ProfileController@buscaMembro] Membros encontrados', [
            'count' => $membros->count(),
            'resultados' => $membros->pluck('id', 'name')
        ]);

        return response()->json($membros);
    }

    /**
     * Exibir formulário de perfil.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Atualizar informações do perfil.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'documento' => 'nullable|string|max:20',
            'sem_cnpj' => 'nullable|boolean',
            'nome_fantasia' => 'nullable|string|max:255',
            'genero' => 'nullable|string|max:20',
            'data_nascimento' => 'nullable|date',
            'igreja_id' => 'nullable|exists:users,id',
            'cep' => 'nullable|string|max:9',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:10',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'telefone' => 'nullable|string|max:20',
            'foto' => 'nullable|image|max:2048',
            'remover_foto' => 'nullable|boolean',
        ]);

        $dados = $request->only([
            'documento', 'nome_fantasia', 'genero', 'data_nascimento',
            'cep', 'endereco', 'numero', 'complemento',
            'bairro', 'cidade', 'estado', 'telefone'
        ]);

        // Se marcou "sem CNPJ", força documento = null
        if ($request->boolean('sem_cnpj')) {
            $dados['documento'] = null;
        }

        // Se for membro (CPF), define type e vincula a igreja
        if ($request->filled('documento') && strlen(preg_replace('/\D/', '', $request->documento)) === 11) {
            $user->type = 'membro';
            $user->igreja_id = $request->igreja_id;
        }

        // 🔹 Define tipo do usuário automaticamente
        $documento = preg_replace('/\D/', '', $request->documento ?? '');
        if ($request->boolean('sem_cnpj') || (strlen($documento) === 14)) {
            $user->type = 'igreja';
            $user->igreja_id = null;
        } else {
            $user->type = 'membro';
        }

        $user->save();

        // Atualiza nome
        $user->update(['name' => $request->name]);

        $dados = $request->only([
            'documento', 'nome_fantasia', 'genero', 'data_nascimento',
            'cep', 'endereco', 'numero', 'complemento',
            'bairro', 'cidade', 'estado', 'telefone'
        ]);

        $detalhes = $user->detalhesUsuario;

        // 🔹 Se o usuário pediu para remover a foto
        if ($request->remover_foto && $detalhes && $detalhes->foto) {
            Storage::disk('public')->delete($detalhes->foto);
            $dados['foto'] = null;
        }

        // 🔹 Se foi enviada nova foto
        if ($request->hasFile('foto')) {
            // Deleta a anterior se existir
            if ($detalhes && $detalhes->foto) {
                Storage::disk('public')->delete($detalhes->foto);
            }

            $caminho = $request->file('foto')->store('usuarios', 'public');
            $dados['foto'] = $caminho;
        }

        // Atualiza ou cria os detalhes
        $user->detalhesUsuario()->updateOrCreate(
            ['user_id' => $user->id],
            $dados
        );

        return back()->with('status', 'profile-updated');
    }

    /**
     * Deletar conta do usuário.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // 🔹 Apagar foto se existir
        if ($user->detalhesUsuario && $user->detalhesUsuario->foto) {
            Storage::disk('public')->delete($user->detalhesUsuario->foto);
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
