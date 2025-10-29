<?php

namespace App\Filament\Resources\NotificacaoResource\Pages;

use App\Filament\Resources\NotificacaoResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListNotificacoes extends ListRecords
{
    protected static string $resource = NotificacaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Criar Notificação'),
        ];
    }
}
