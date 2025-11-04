<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Comissao extends Model
{
    use HasFactory;

    protected $table = 'comissoes';

    protected $fillable = [
        'ministerio_id',
        'membro_id',
        'funcao',
        'observacoes',
        'data_entrada',
        'data_saida',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'data_entrada' => 'date',
        'data_saida' => 'date',
    ];

    /**
     * Relações
     */
    public function ministerio()
    {
        return $this->belongsTo(Ministerio::class);
    }

    public function membro()
    {
        return $this->belongsTo(User::class, 'membro_id');
    }

    /**
     * 🔄 Sincroniza automaticamente o status da comissão com as datas.
     * - Ativa se estiver dentro do período válido.
     * - Inativa se a data atual estiver fora do intervalo.
     */
    public function sincronizarStatus()
    {
        $hoje = Carbon::today();

        // Se não tiver data de entrada, consideramos ativo somente se o campo estiver marcado
        if (!$this->data_entrada) {
            return $this->ativo;
        }

        $inicio = Carbon::parse($this->data_entrada);
        $fim = $this->data_saida ? Carbon::parse($this->data_saida) : null;

        $ativoAutomatico = $fim
            ? $hoje->between($inicio, $fim)
            : $hoje->greaterThanOrEqualTo($inicio);

        // Só altera se o status automático for diferente do atual
        if ($this->ativo !== $ativoAutomatico) {
            $this->ativo = $ativoAutomatico;
            $this->saveQuietly();
        }

        return $this->ativo;
    }

    /**
     * Boot — garante sincronização automática ao carregar ou salvar.
     */
    protected static function booted()
    {
        static::retrieved(function ($comissao) {
            $comissao->sincronizarStatus();
        });

        static::saving(function ($comissao) {
            $comissao->sincronizarStatus();
        });
    }
}
