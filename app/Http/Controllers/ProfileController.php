<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
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
            'nome_fantasia' => 'nullable|string|max:255',
            'genero' => 'nullable|string|max:20',
            'data_nascimento' => 'nullable|date',
            'cep' => 'nullable|string|max:9',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:10',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'telefone' => 'nullable|string|max:20',
            'foto' => 'nullable|image|max:2048', // até 2MB
            'remover_foto' => 'nullable|boolean',
        ]);

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
