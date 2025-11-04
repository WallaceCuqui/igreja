<?php

namespace App\Http\Controllers;

use App\Models\Lideranca;
use App\Models\Ministerio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LiderancaController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // 🔒 Apenas igrejas podem acessar essa tela
        if (! $user->isIgreja()) {
            abort(403, 'Apenas igrejas podem acessar a gestão de lideranças.');
        }

        // Busca ministérios da igreja logada
        $ministerios = Ministerio::where('igreja_id', $user->id)
            ->orderBy('nome')
            ->get();

        // 🔒 Se a igreja não tem ministérios, não faz sentido mostrar a tela
        if ($ministerios->isEmpty()) {
            return redirect()->route('dashboard')
                ->with('warning', 'Nenhum ministério vinculado à sua igreja. Cadastre um ministério primeiro.');
        }

        Log::info('👤 [LiderancaController@index] Acessando lista de lideranças.', ['igreja_id' => $user->id]);

        // Carrega apenas as lideranças da igreja logada
        $liderancas = Lideranca::with(['ministerio', 'lider', 'vice'])
            ->whereHas('ministerio', function ($q) use ($user) {
                $q->where('igreja_id', $user->id);
            })
            ->orderByDesc('data_inicio')
            ->get();

        $usuarios = User::orderBy('name')->get();

        $editando = null;
        if ($request->has('edit')) {
            $editando = Lideranca::find($request->get('edit'));

            // 🔒 Protege o modo de edição também
            if (! $editando || $editando->ministerio->igreja_id !== $user->id) {
                abort(403, 'Você não tem permissão para editar essa liderança.');
            }

            Log::info('✏️ [LiderancaController@index] Editando liderança.', ['id' => $editando->id]);
        }

        return view('ministerios.liderancas', compact('liderancas', 'ministerios', 'usuarios', 'editando'));
    }


    public function store(Request $request)
    {
        $user = auth()->user();

        // 🔒 Só igreja pode cadastrar
        if (! $user->isIgreja()) {
            abort(403, 'Apenas igrejas podem cadastrar lideranças.');
        }

        Log::info('📝 [LiderancaController@store] Iniciando cadastro de liderança.', ['dados' => $request->all()]);

        $validated = $request->validate([
            'ministerio_id' => 'required|exists:ministerios,id',
            'lider_id' => 'required|exists:users,id',
            'vice_id' => 'nullable|exists:users,id',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'ativo' => 'nullable|boolean',
        ]);

        // 🛡️ Verifica se o ministério pertence à igreja logada
        $ministerio = Ministerio::findOrFail($validated['ministerio_id']);
        if ($ministerio->igreja_id !== $user->id) {
            abort(403, 'Você não pode adicionar liderança em ministério de outra igreja.');
        }

        $validated['ativo'] = $request->has('ativo');

        Lideranca::updateOrCreate(['id' => $request->id], $validated);

        return redirect()->route('ministerios.liderancas.index')
            ->with('success', 'Liderança cadastrada com sucesso!');
    }

    public function update(Request $request, Lideranca $lideranca)
    {
        $user = auth()->user();

        // 🔒 Apenas igreja dona do ministério pode atualizar
        if (! $user->isIgreja() || $lideranca->ministerio->igreja_id !== $user->id) {
            abort(403, 'Você não tem permissão para alterar essa liderança.');
        }

        Log::info('🛠️ [LiderancaController@update] Atualizando liderança.', ['id' => $lideranca->id]);

        $validated = $request->validate([
            'ministerio_id' => 'required|exists:ministerios,id',
            'lider_id' => 'required|exists:users,id',
            'vice_id' => 'nullable|exists:users,id',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'ativo' => 'boolean',
        ]);

        $lideranca->update($validated);

        return redirect()->route('ministerios.liderancas.index')
            ->with('success', 'Liderança atualizada com sucesso!');
    }

    public function destroy(Lideranca $lideranca)
    {
        $user = auth()->user();

        // 🔒 Apenas igreja dona pode excluir
        if (! $user->isIgreja() || $lideranca->ministerio->igreja_id !== $user->id) {
            abort(403, 'Você não tem permissão para excluir essa liderança.');
        }

        Log::info('🗑️ [LiderancaController@destroy] Excluindo liderança.', ['id' => $lideranca->id]);

        $lideranca->delete();

        return redirect()->route('ministerios.liderancas.index')
            ->with('success', 'Liderança excluída com sucesso!');
    }
}
