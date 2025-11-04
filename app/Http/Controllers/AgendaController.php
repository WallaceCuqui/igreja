<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\Agenda;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function index()
    {
        Log::info('Acessou a lista de agendas dos ministérios.');
        return view('ministerios.agendas');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ministerio_id' => 'required|exists:ministerios,id',
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'local' => 'nullable|string|max:255',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date',
            'responsavel_id' => 'nullable|exists:users,id',
            'status' => 'required|string',
            'tipo_evento' => 'nullable|string|max:255',
        ]);

        $agenda = Agenda::create($validated);
        return response()->json($agenda, 201);
    }

    public function show(Agenda $agenda)
    {
        return response()->json($agenda->load(['ministerio', 'responsavel']));
    }

    public function update(Request $request, Agenda $agenda)
    {
        $validated = $request->validate([
            'titulo' => 'sometimes|string|max:255',
            'descricao' => 'nullable|string',
            'local' => 'nullable|string|max:255',
            'data_inicio' => 'sometimes|date',
            'data_fim' => 'nullable|date',
            'status' => 'string',
            'tipo_evento' => 'nullable|string|max:255',
        ]);

        $agenda->update($validated);
        return response()->json($agenda);
    }

    public function destroy(Agenda $agenda)
    {
        $agenda->delete();
        return response()->json(null, 204);
    }
}
