<?php

namespace App\Http\Controllers;

use App\Models\Ministerio;
use Illuminate\Http\Request;

class MinisterioController extends Controller
{
    public function index()
    {
        $ministerios = Ministerio::with(['igreja', 'liderancas'])->get();
        return response()->json($ministerios);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'data_fundacao' => 'nullable|date',
            'igreja_id' => 'required|exists:users,id',
        ]);

        $ministerio = Ministerio::create($validated);
        return response()->json($ministerio, 201);
    }

    public function show(Ministerio $ministerio)
    {
        return response()->json($ministerio->load(['igreja', 'liderancas', 'comissoes', 'integrantes', 'agendas']));
    }

    public function update(Request $request, Ministerio $ministerio)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'data_fundacao' => 'nullable|date',
            'ativo' => 'boolean',
        ]);

        $ministerio->update($validated);
        return response()->json($ministerio);
    }

    public function destroy(Ministerio $ministerio)
    {
        $ministerio->delete();
        return response()->json(null, 204);
    }
}
