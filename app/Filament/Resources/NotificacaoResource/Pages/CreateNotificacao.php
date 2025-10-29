<?php

namespace App\Filament\Resources\NotificacaoResource\Pages;

use App\Filament\Resources\NotificacaoResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateNotificacao extends CreateRecord
{
    protected static string $resource = NotificacaoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // garante que created_by seja o user logado
        $data['created_by'] = Auth::id();

        // se target_user_id estiver vazio, manter null (broadcast)
        if (empty($data['target_user_id'])) {
            $data['target_user_id'] = null;
        }

        return $data;
    }
}
