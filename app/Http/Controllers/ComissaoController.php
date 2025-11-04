<?php

namespace App\Http\Controllers;

use App\Models\Comissao;
use Illuminate\Http\Request;

class ComissaoController extends Controller
{
    public function index()
    {
        return view('ministerios.comissoes');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ministerio_id' => 'required|exists:ministerios,id',
            'membro_id' => 'required|exists:users,id',
            'funcao' => 'required|string|max:255',
            'data_entrada' => 'nullable|date',
            'data_saida' => 'nullable|date',
            'observacoes' => 'nullable|string',
        ]);

        $comissao = Comissao::create($validated);
        return response()->json($comissao, 201);
    }

    public function show(Comissao $comissao)
    {
        return response()->json($comissao->load(['ministerio', 'membro']));
    }

    public function update(Request $request, Comissao $comissao)
    {
        $validated = $request->validate([
            'funcao' => 'sometimes|string|max:255',
            'data_saida' => 'nullable|date',
            'observacoes' => 'nullable|string',
        ]);

        $comissao->update($validated);
        return response()->json($comissao);
    }

    public function destroy(Comissao $comissao)
    {
        $comissao->delete();
        return response()->json(null, 204);
    }
}
