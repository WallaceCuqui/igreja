<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LiderancaResource\Pages;
use App\Models\Lideranca;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\{Select, DatePicker, Toggle};
use Filament\Tables\Columns\{TextColumn, BooleanColumn};
use Filament\Tables\Actions\{EditAction};

//Para verificar as permissões
use App\Filament\Resources\Traits\HasModuleAccess;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;


class LiderancaResource extends Resource
{
    // só define qual módulo será usado
    // o nome do módulo deve ser igual ao definido em config/modules.php
    use HasModuleAccess;
    protected static string $moduleForAccess = 'liderancas';

    protected static ?string $model = Lideranca::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Lideranças';
    protected static ?string $pluralLabel = 'Lideranças';
    protected static ?string $slug = 'liderancas';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('ministerio_id')
                ->relationship('ministerio', 'nome')
                ->required()
                ->label('Ministério'),

            Select::make('lider_id')
                ->relationship('lider', 'name')
                ->required()
                ->label('Líder'),

            Select::make('vice_id')
                ->relationship('vice', 'name')
                ->nullable()
                ->label('Vice-líder'),

            DatePicker::make('data_inicio')->required()->label('Início'),
            DatePicker::make('data_fim')->label('Fim'),
            Toggle::make('ativo')->label('Ativo')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('ministerio.nome')->label('Ministério')->sortable()->searchable(),
            TextColumn::make('lider.name')->label('Líder')->sortable()->searchable(),
            TextColumn::make('vice.name')->label('Vice')->sortable()->searchable(),
            TextColumn::make('data_inicio')->label('Início')->date(),
            TextColumn::make('data_fim')->label('Fim')->date(),
            BooleanColumn::make('ativo')->label('Ativo'),
        ])
        ->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make()
                ->visible(fn ($record) => static::canEdit($record)),
            Tables\Actions\DeleteAction::make()
                ->visible(fn ($record) => static::canDelete($record))
                ->authorize(fn ($record) => static::canDelete($record)),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make()
                ->visible(fn () => static::checkAccess('delete'))
                ->authorize(fn () => static::checkAccess('delete')),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLiderancas::route('/'),
            'create' => Pages\CreateLideranca::route('/create'),
            'edit' => Pages\EditLideranca::route('/{record}/edit'),
        ];
    }
}
