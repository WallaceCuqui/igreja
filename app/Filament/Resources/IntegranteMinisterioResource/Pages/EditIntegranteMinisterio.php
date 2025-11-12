<?php

namespace App\Filament\Resources\IntegranteMinisterioResource\Pages;

use App\Filament\Resources\IntegranteMinisterioResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIntegranteMinisterio extends EditRecord
{
    protected static string $resource = IntegranteMinisterioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
