<?php

namespace App\Http\Controllers;

use App\Models\Ministerio;
use App\Models\Agenda;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    /**
     * Lista as agendas e exibe o formulário de criação/edição.
     */
    public function index(Ministerio $ministerio, Request $request)
    {
        $editando = null;

        if ($request->has('edit')) {
            $editando = Agenda::where('ministerio_id', $ministerio->id)
                ->find($request->get('edit'));
        }

        $agendas = Agenda::where('ministerio_id', $ministerio->id)
            ->orderBy('data_inicio', 'desc')
            ->get();

        return view('ministerios.agendas', compact('ministerio', 'agendas', 'editando'));
    }

    /**
     * Armazena um novo evento.
     */
    public function store(Request $request, Ministerio $ministerio)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'local' => 'nullable|string|max:255',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'status' => 'required|in:planejado,realizado,cancelado',
            'tipo_evento' => 'nullable|string|max:255',
        ]);

        $validated['ministerio_id'] = $ministerio->id;

        Agenda::create($validated);

        return redirect()
            ->route('ministerios.agendas.index', $ministerio->id)
            ->with('success', 'Evento criado com sucesso!');
    }

    /**
     * Atualiza um evento existente.
     */
    public function update(Request $request, Ministerio $ministerio, Agenda $agenda)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'local' => 'nullable|string|max:255',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'status' => 'required|in:planejado,realizado,cancelado',
            'tipo_evento' => 'nullable|string|max:255',
        ]);

        $agenda->update($validated);

        return redirect()
            ->route('ministerios.agendas.index', $ministerio->id)
            ->with('success', 'Evento atualizado com sucesso!');
    }

    /**
     * Exclui um evento.
     */
    public function destroy(Ministerio $ministerio, Agenda $agenda)
    {
        if ($agenda->ministerio_id !== $ministerio->id) {
            abort(403, 'Acesso negado.');
        }

        $agenda->delete();

        return redirect()
            ->route('ministerios.agendas.index', $ministerio->id)
            ->with('success', 'Evento excluído com sucesso!');
    }
}
