<?php

namespace App\Http\Controllers;

use App\Models\IntegranteMinisterio;
use Illuminate\Http\Request;

class IntegranteMinisterioController extends Controller
{
    public function index()
    {
        return view('ministerios.integrantes');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ministerio_id' => 'required|exists:ministerios,id',
            'membro_id' => 'required|exists:users,id',
            'tipo_vinculo' => 'required|string',
            'data_entrada' => 'nullable|date',
            'observacoes' => 'nullable|string',
        ]);

        $integrante = IntegranteMinisterio::create($validated);
        return response()->json($integrante, 201);
    }

    public function show(IntegranteMinisterio $integrante)
    {
        return response()->json($integrante->load(['ministerio', 'membro']));
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
