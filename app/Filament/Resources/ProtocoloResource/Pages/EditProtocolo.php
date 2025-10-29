<?php

namespace App\Filament\Resources\ProtocoloResource\Pages;

use App\Filament\Resources\ProtocoloResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

use Throwable;

class EditProtocolo extends EditRecord
{
    protected static string $resource = ProtocoloResource::class;

    protected function afterSave(): void
    {
        /*\Log::info('EditProtocolo::afterSave called', [
            'protocolo_id' => $this->record->id ?? null,
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
        ]);*/

        $state = $this->form->getState(true);
        $request = request();

        // Função que tenta várias fontes comuns
        $extractNovaMensagem = function () use ($state, $request) {
            // 1) state direto
            if (is_array($state) && array_key_exists('nova_mensagem', $state) && is_string($state['nova_mensagem']) && trim($state['nova_mensagem']) !== '') {
                return trim($state['nova_mensagem']);
            }

            // 2) request direto
            $rq = $request->input('nova_mensagem');
            if (is_string($rq) && trim($rq) !== '') {
                return trim($rq);
            }

            // 3) components[*].updates (já tentado antes, mantemos)
            $components = $request->input('components');
            if (is_array($components)) {
                foreach ($components as $c) {
                    if (is_array($c) && array_key_exists('updates', $c) && is_array($c['updates'])) {
                        foreach ($c['updates'] as $uk => $uv) {
                            if (($uk === 'nova_mensagem' || $uk === 'data.nova_mensagem' || str_ends_with((string) $uk, '.nova_mensagem')) && is_string($uv) && trim($uv) !== '') {
                                return trim($uv);
                            }
                        }
                    }
                }
            }

            // 4) components[*].snapshot (caso seu payload coloque o estado no snapshot JSON)
            if (is_array($components)) {
                foreach ($components as $c) {
                    if (is_array($c) && array_key_exists('snapshot', $c) && is_string($c['snapshot'])) {
                        $decoded = json_decode($c['snapshot'], true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            // procurar em decoded.data (conforme seu log)
                            if (array_key_exists('data', $decoded) && is_array($decoded['data'])) {
                                // caso data contenha 'data' com lista de registros (visto no log)
                                if (array_key_exists('data', $decoded['data']) && is_array($decoded['data']['data'])) {
                                    foreach ($decoded['data']['data'] as $item) {
                                        if (is_array($item) && array_key_exists('nova_mensagem', $item) && is_string($item['nova_mensagem']) && trim($item['nova_mensagem']) !== '') {
                                            return trim($item['nova_mensagem']);
                                        }
                                    }
                                }

                                // caso data seja associaivo e contenha 'nova_mensagem'
                                if (array_key_exists('nova_mensagem', $decoded['data']) && is_string($decoded['data']['nova_mensagem']) && trim($decoded['data']['nova_mensagem']) !== '') {
                                    return trim($decoded['data']['nova_mensagem']);
                                }
                            }
                        }
                    }
                }
            }

            // 5) varredura recursiva como último recurso
            $all = $request->all();
            $stack = [$all];
            while ($stack) {
                $current = array_pop($stack);
                if (!is_array($current)) {
                    continue;
                }
                foreach ($current as $k => $v) {
                    if (is_string($k) && (str_ends_with($k, 'nova_mensagem') || $k === 'nova_mensagem')) {
                        if (is_string($v) && trim($v) !== '') {
                            return trim($v);
                        }
                    }
                    if (is_array($v)) {
                        $stack[] = $v;
                    }
                }
            }

            return null;
        };

        $novaMensagem = $extractNovaMensagem();

        /*\Log::debug('Captured states', [
            'state_keys' => is_array($state) ? array_keys($state) : null,
            'request_preview' => is_array($request->all()) ? array_slice($request->all(), 0, 5) : null,
            'nova_mensagem_extracted' => $novaMensagem !== null,
        ]);*/

        if (!is_string($novaMensagem) || trim($novaMensagem) === '') {
            //\Log::info('Nenhuma nova_mensagem enviada — nada a fazer.', ['protocolo_id' => $this->record->id]);
            return;
        }

        $novaMensagem = trim($novaMensagem);

        \DB::beginTransaction();
        try {
            if (empty($this->record->atendido_por)) {
                $this->record->update(['atendido_por' => \Illuminate\Support\Facades\Auth::id()]);
                \Log::info('atendido_por set', ['protocolo_id' => $this->record->id, 'atendido_por' => $this->record->fresh()->atendido_por]);
            } else {
                \Log::info('atendido_por already set', ['protocolo_id' => $this->record->id, 'atendido_por' => $this->record->atendido_por]);
            }

            $mensagem = $this->record->mensagens()->create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'mensagem' => $novaMensagem,
                'is_staff' => true,
            ]);

            //\Log::info('ProtocoloMensagem criada', ['protocolo_id' => $this->record->id, 'mensagem_id' => $mensagem->id ?? null]);

            \DB::commit();

            $this->form->fill(['nova_mensagem' => null]);
            $this->notify('success', 'Resposta enviada e registrada com sucesso.');
        } catch (\Throwable $e) {
            \DB::rollBack();
            /*\Log::error('Erro ao salvar resposta do protocolo', [
                'protocolo_id' => $this->record->id ?? null,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
            ]);*/

            Notification::make()
            ->success()
            ->title('Danger')
            ->body('Erro ao salvar a resposta. Verifique os logs.')
            ->send();

        }
    }


}
