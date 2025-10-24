<?php

namespace App\Filament\Resources\ProtocoloResource\Pages;

use App\Filament\Resources\ProtocoloResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProtocolo extends EditRecord
{
    protected static string $resource = ProtocoloResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
