<?php

namespace App\Filament\Resources\AuditResource\Pages;

use App\Filament\Resources\AuditResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;

class ListAudits extends ListRecords
{
    protected static string $resource = AuditResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('event')
                ->label('Tipo de Ação')
                ->options([
                    'created' => 'Criado',
                    'updated' => 'Atualizado',
                    'deleted' => 'Excluído',
                ]),
        ];
    }
}
