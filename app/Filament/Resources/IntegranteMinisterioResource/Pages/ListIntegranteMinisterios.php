<?php

namespace App\Filament\Resources\IntegranteMinisterioResource\Pages;

use App\Filament\Resources\IntegranteMinisterioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIntegranteMinisterios extends ListRecords
{
    protected static string $resource = IntegranteMinisterioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
