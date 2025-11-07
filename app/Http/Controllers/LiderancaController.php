<?php

namespace App\Http\Controllers;

use App\Models\Lideranca;
use App\Models\Ministerio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LiderancaController extends Controller
{
    public function index(Ministerio $ministerio, Request $request)
    {
        $user = auth()->user();

        if (! $user->isIgreja() || $ministerio->igreja_id !== $user->id) {
            abort(403, 'Acesso não autorizado ao ministério.');
        }

        $liderancas = Lideranca::with(['lider', 'vice'])
            ->where('ministerio_id', $ministerio->id)
            ->orderByDesc('data_inicio')
            ->get();

        $usuarios = User::where('igreja_id', $user->id)->orderBy('name')->get();

        $editando = null;
        if ($request->filled('edit') && is_numeric($request->get('edit'))) {
            $editando = Lideranca::find($request->get('edit'));

            if (! $editando || $editando->ministerio_id !== $ministerio->id) {
                abort(403, 'Você não tem permissão para editar essa liderança.');
            }
        }

        return view('ministerios.liderancas', compact(
            'ministerio',
            'liderancas',
            'usuarios',
            'editando'
        ));
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
