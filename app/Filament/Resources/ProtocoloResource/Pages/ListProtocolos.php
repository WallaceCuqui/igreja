<?php

namespace App\Filament\Resources\ProtocoloResource\Pages;

use App\Filament\Resources\ProtocoloResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProtocolos extends ListRecords
{
    protected static string $resource = ProtocoloResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
