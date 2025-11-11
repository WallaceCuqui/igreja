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

    public function calendario()
    {
        $agendas = Agenda::with(['ministerio', 'criador'])->get();

        $eventos = $agendas->map(function ($agenda) {
            return [
                'title' => "{$agenda->ministerio->nome}: {$agenda->titulo}",
                'start' => $agenda->data_inicio->format('Y-m-d\TH:i:s'),
                'end' => optional($agenda->data_fim)->format('Y-m-d\TH:i:s'),
                'color' => match ($agenda->status) {
                    'planejado' => '#3b82f6',
                    'realizado' => '#16a34a',
                    'cancelado' => '#dc2626',
                    default => '#6b7280'
                },
                'extendedProps' => [
                    'descricao' => $agenda->descricao,
                    'local' => $agenda->local,
                    'tipo_evento' => $agenda->tipo_evento,
                    'criado_por' => optional($agenda->criador)->name,
                ],
            ];
        })->values();

        // 👇 Não use toJson() aqui — deixe o Blade fazer o encode corretamente
        return view('ministerios.calendario', [
            'eventosJson' => $eventos,
        ]);
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
        $validated['criado_por'] = auth()->id();

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
