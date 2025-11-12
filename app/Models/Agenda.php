<?php
// app/Models/Agenda.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\NotificacaoController;


class Agenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'ministerio_id',
        'titulo',
        'descricao',
        'local',
        'data_inicio',
        'data_fim',
        'criado_por',
        'status',
        'tipo_evento',
    ];

    protected $casts = [
        'data_inicio' => 'datetime',
        'data_fim' => 'datetime',
    ];

    protected static function booted()
    {
        // 🔹 Quando a agenda for criada
        static::created(function ($agenda) {
            try {
                NotificacaoController::criarNotificacaoMinisterio(
                    $agenda->ministerio_id,
                    'Nova agenda criada',
                    "Uma nova agenda foi adicionada: {$agenda->titulo}",
                    $agenda->criado_por
                );
            } catch (\Throwable $e) {
                \Log::error('Erro ao criar notificação de nova agenda', [
                    'agenda_id' => $agenda->id,
                    'erro' => $e->getMessage(),
                ]);
            }
        });

        // 🔹 Quando a agenda for atualizada
        static::updated(function ($agenda) {
            try {
                // Evita enviar notificação se não houve mudança relevante
                if ($agenda->wasChanged(['titulo', 'data', 'hora_inicio', 'hora_fim', 'local'])) {
                    NotificacaoController::criarNotificacaoMinisterio(
                        $agenda->ministerio_id,
                        'Agenda atualizada',
                        "A agenda '{$agenda->titulo}' foi atualizada.",
                        $agenda->criado_por
                    );
                }
            } catch (\Throwable $e) {
                \Log::error('Erro ao criar notificação de atualização de agenda', [
                    'agenda_id' => $agenda->id,
                    'erro' => $e->getMessage(),
                ]);
            }
        });

        // 🔹 Quando a agenda for deletada
        static::deleted(function ($agenda) {
            try {
                NotificacaoController::criarNotificacaoMinisterio(
                    $agenda->ministerio_id,
                    'Agenda cancelada',
                    "A agenda '{$agenda->titulo}' foi cancelada ou removida.",
                    $agenda->criado_por
                );
            } catch (\Throwable $e) {
                \Log::error('Erro ao criar notificação de cancelamento de agenda', [
                    'agenda_id' => $agenda->id,
                    'erro' => $e->getMessage(),
                ]);
            }
        });
    }

    // 🔗 Relacionamentos
    public function ministerio()
    {
        return $this->belongsTo(Ministerio::class);
    }

    public function criador()
    {
        return $this->belongsTo(User::class, 'criado_por');
    }
}
