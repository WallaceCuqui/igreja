<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Relacao;
use App\Models\Ministerio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RelacoesController extends Controller
{
    use AuthorizesRequests;

    // Página única
    public function index(Request $request)
    {
        $user = Auth::user();
        $ministerios = Ministerio::all();
        $tiposRelacao = [
            'filho' => 'Filho(a)',
            'conjuge' => 'Cônjuge',
            'pai' => 'Pai',
            'mae' => 'Mãe',
            'avo' => 'Avô(ó)',
            'outro' => 'Outro vínculo',
        ];
        $relacoes = $user->relacoes;
        $usuariosAdolescentes = User::whereHas('detalhesUsuario', function ($q) {
            $q->whereYear('data_nascimento', '<=', now()->subYears(12)->year);
        })->where('id', '!=', $user->id)->get();

        $editRelacao = null;
        if ($request->has('edit')) {
            $editRelacao = Relacao::find($request->edit);
        }

        return view('profile.relacoes', compact(
            'user', 'relacoes', 'ministerios', 'tiposRelacao', 'usuariosAdolescentes', 'editRelacao'
        ));
    }




    // Cadastrar relacao/dependente
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

        $validated['membro_id'] = Auth::id();

        // Upload da foto (se tiver)
        if ($request->hasFile('foto')) {
            try {
                if (!\Storage::disk('public')->exists('relacoes')) {
                    \Storage::disk('public')->makeDirectory('relacoes');
                }

                $validated['foto'] = $request->file('foto')->store('relacoes', 'public');
            } catch (\Exception $e) {
                return back()->withErrors(['foto' => 'Erro ao salvar a foto: ' . $e->getMessage()]);
            }
        }


        $relacao = Relacao::create($validated);

        // Vincular ministérios
        if ($request->has('ministerios')) {
            $relacao->ministerios()->sync($request->ministerios);
        }

        return redirect()->route('profile.relacoes')->with('success', 'Relacão cadastrado com sucesso.');
    }

    // Vincular adolescente já cadastrado
    public function vincularAdolescente(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        Relacao::firstOrCreate([
            'membro_id' => $user->id,
            'nome' => $data['user_id'], // Aqui você pode vincular de acordo com a lógica que preferir
        ]);

        return redirect()->route('profile.relacoes')->with('success', 'Adolescente vinculado com sucesso.');
    }

    // Editar relacao
    public function editRelacao(Relacao $relacao)
    {
        $user = Auth::user();

        if ($relacao->membro_id !== $user->id) {
            abort(403, 'Ação não autorizada.');
        }

        $ministerios = Ministerio::all();
        $tiposRelacao = [
            'filho' => 'Filho(a)',
            'conjuge' => 'Cônjuge',
            'pai' => 'Pai',
            'mae' => 'Mãe',
            'avo' => 'Avô(ó)',
            'outro' => 'Outro vínculo',
        ];

        // Passa a variável $relacao e uma flag $editar
        return view('profile.relacoes', compact('relacao', 'ministerios', 'tiposRelacao'));
    }


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
        ]);

        $relacao->update($data);

        return redirect()->route('profile.relacoes')->with('success', 'Relacao atualizado com sucesso.');
    }

    public function destroyRelacao(Relacao $relacao)
    {
        $user = Auth::user();

        if ($relacao->membro_id !== $user->id) {
            abort(403, 'Ação não autorizada.');
        }

        $relacao->delete();

        return redirect()->route('profile.relacoes')->with('success', 'Relacao removido com sucesso.');
    }

}
