<?php

namespace App\Filament\Resources\ProtocoloResource\Pages;

use App\Filament\Resources\ProtocoloResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\ProtocoloMensagem;


class EditProtocolo extends EditRecord
{
    protected static string $resource = ProtocoloResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (filled($data['nova_mensagem'] ?? null)) {
            ProtocoloMensagem::create([
                'protocolo_id' => $this->record->id,
                'user_id' => auth()->id(),
                'mensagem' => $data['nova_mensagem'],
                'is_staff' => true,
            ]);
        }

        return $data;
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
