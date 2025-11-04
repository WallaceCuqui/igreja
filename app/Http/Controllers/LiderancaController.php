<?php

namespace App\Http\Controllers;

use App\Models\Lideranca;
use Illuminate\Http\Request;

class LiderancaController extends Controller
{
    public function index()
    {
        return view('ministerios.liderancas');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ministerio_id' => 'required|exists:ministerios,id',
            'lider_id' => 'required|exists:users,id',
            'vice_id' => 'nullable|exists:users,id',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date',
            'status' => 'required|string',
        ]);

        $lideranca = Lideranca::create($validated);
        return response()->json($lideranca, 201);
    }

    public function show(Lideranca $lideranca)
    {
        return response()->json($lideranca->load(['ministerio', 'lider', 'vice']));
    }

    public function update(Request $request, Lideranca $lideranca)
    {
        $validated = $request->validate([
            'lider_id' => 'sometimes|exists:users,id',
            'vice_id' => 'nullable|exists:users,id',
            'data_inicio' => 'sometimes|date',
            'data_fim' => 'nullable|date',
            'status' => 'string',
        ]);

        $lideranca->update($validated);
        return response()->json($lideranca);
    }

    public function destroy(Lideranca $lideranca)
    {
        $lideranca->delete();
        return response()->json(null, 204);
    }
}
