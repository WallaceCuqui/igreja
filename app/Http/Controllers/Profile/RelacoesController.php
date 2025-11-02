<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Relacao;
use App\Models\Ministerio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RelacoesController extends Controller
{
    // Página única
    public function index(Request $request)
    {
        $user = Auth::user();
        $ministerios = Ministerio::all();
        $tiposRelacao = [
            'dependente' => 'Filho / Enteado',
            'conjuge' => 'Cônjuge',
            'pai' => 'Pai',
            'mae' => 'Mãe',
            'avo' => 'Avô(ó)',
            'outro' => 'Outro vínculo',
        ];

        $relacoes = $user->relacoes;
        $usuariosRelacoes = User::where('id', '!=', $user->id)->get();

        $editRelacao = null;
        if ($request->has('edit')) {
            $editRelacao = Relacao::find($request->edit);
        }

        return view('profile.relacoes', compact(
            'user', 'relacoes', 'ministerios', 'tiposRelacao', 'usuariosRelacoes', 'editRelacao'
        ));
    }

    // Buscar usuários para vincular
    public function buscarUsuarios(Request $request)
    {
        $q = $request->get('q', '');
        if (strlen($q) < 2) return response()->json([]);

        $users = \App\Models\User::query()
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json($users);
    }



    // Cadastrar nova relação (dependente)
    public function storeRelacao(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'data_nascimento' => 'nullable|date',
            'sexo' => 'nullable|in:M,F',
            'tipo' => 'required|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'ministerios' => 'array',
        ]);

        $validated['membro_id'] = $user->id;

        // Upload da foto
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('relacoes', 'public');
        }

        $relacao = Relacao::create($validated);

        // Vincular ministérios
        if ($request->has('ministerios')) {
            $relacao->ministerios()->sync($request->ministerios);
        }

        // 🔄 Verifica se o usuário tem cônjuge
        $conjuge = Relacao::where('membro_id', $user->id)
            ->where('tipo', 'conjuge')
            ->first();

        if ($conjuge && $conjuge->relacionado_id) {
            // cria uma cópia da relação para o cônjuge
            Relacao::firstOrCreate([
                'membro_id' => $conjuge->relacionado_id,
                'nome' => $relacao->nome,
                'data_nascimento' => $relacao->data_nascimento,
                'sexo' => $relacao->sexo,
                'tipo' => $relacao->tipo,
                'foto' => $relacao->foto,
            ]);
        }

        return redirect()->route('profile.relacoes')->with('success', 'Relação cadastrada com sucesso.');
    }

    // Vincular relação já cadastrada (bidirecional)
    public function vincularRelacao(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'tipo' => 'required|string',
        ]);

        // Cria o vínculo (pai → filho)
        Relacao::firstOrCreate([
            'membro_id' => $user->id,
            'relacionado_id' => $data['user_id'],
            'tipo' => $data['tipo'],
        ]);

        // Cria o vínculo espelho (filho → pai)
        Relacao::firstOrCreate([
            'membro_id' => $data['user_id'],
            'relacionado_id' => $user->id,
            'tipo' => $this->tipoEspelho($data['tipo']),
        ]);

        return redirect()->route('profile.relacoes')->with('success', 'Relações vinculadas com sucesso.');
    }

    // Define o tipo espelho (pai <-> filho)
    private function tipoEspelho($tipo)
    {
        return match ($tipo) {
            'pai' => 'filho',
            'mae' => 'filho',
            'filho', 'dependente' => 'pai',
            'conjuge' => 'conjuge',
            'avo' => 'neto',
            'neto' => 'avo',
            default => 'outro',
        };
    }

    // Atualizar
    public function updateRelacao(Request $request, Relacao $relacao)
    {
        $user = Auth::user();

        if ($relacao->membro_id !== $user->id) {
            abort(403, 'Ação não autorizada.');
        }

        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'data_nascimento' => 'nullable|date',
            'sexo' => 'nullable|in:M,F',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('relacoes', 'public');
        }

        $relacao->update($data);

        return redirect()->route('profile.relacoes')->with('success', 'Relação atualizada com sucesso.');
    }

    // Deletar
    public function destroyRelacao(Relacao $relacao)
    {
        $user = Auth::user();

        if ($relacao->membro_id !== $user->id) {
            abort(403, 'Ação não autorizada.');
        }

        $relacao->delete();

        return redirect()->route('profile.relacoes')->with('success', 'Relação removida com sucesso.');
    }
}
