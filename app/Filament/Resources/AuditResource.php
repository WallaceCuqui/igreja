<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditResource\Pages;
use App\Models\Audit;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class AuditResource extends Resource
{
    protected static ?string $model = Audit::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Auditoria';
    protected static ?string $navigationGroup = 'Sistema';
    protected static ?int $navigationSort = 99;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Usuário')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('model')
                    ->label('Modelo')
                    ->sortable()
                    ->searchable(),

                BadgeColumn::make('event')
                    ->label('Ação')
                    ->colors([
                        'success' => 'created',
                        'warning' => 'updated',
                        'danger' => 'deleted',
                    ])
                    ->sortable(),

                TextColumn::make('model_id')
                    ->label('ID do Registro')
                    ->sortable(),

                TextColumn::make('old_values')
                    ->label('Antes')
                    ->formatStateUsing(fn ($state) => json_encode(json_decode($state, true), JSON_PRETTY_PRINT))
                    ->copyable()
                    ->limit(80),

                TextColumn::make('new_values')
                    ->label('Depois')
                    ->formatStateUsing(fn ($state) => json_encode(json_decode($state, true), JSON_PRETTY_PRINT))
                    ->copyable()
                    ->limit(80),


                TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAudits::route('/'),
        ];
    }

    public static function getModelLabel(): string
    {
        return 'Registro de Auditoria';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Auditorias';
    }
}
