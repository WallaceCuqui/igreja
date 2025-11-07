<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MinisterioResource\Pages;
use App\Models\Ministerio;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\{TextInput, Textarea, Select, DatePicker, Toggle};
use Filament\Tables\Columns\{TextColumn, BooleanColumn, BadgeColumn};
use Filament\Tables\Actions\{EditAction};


//Para verificar as permissões
use App\Filament\Resources\Traits\HasModuleAccess;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;



class MinisterioResource extends Resource
{
    // só define qual módulo será usado
    // o nome do módulo deve ser igual ao definido em config/modules.php
    use HasModuleAccess;
    protected static string $moduleForAccess = 'ministerios';

    protected static ?string $model = Ministerio::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';
    protected static ?string $navigationLabel = 'Ministérios';
    protected static ?string $pluralLabel = 'Ministérios';
    protected static ?string $slug = 'ministerios';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('nome')->required()->maxLength(255),
            Textarea::make('descricao')->rows(3),
            Select::make('politica_ingresso')
                ->options([
                    'aberto' => 'Aberto',
                    'restrito' => 'Restrito',
                ])
                ->default('restrito')
                ->required(),
            DatePicker::make('data_fundacao'),
            Toggle::make('ativo')->label('Está ativo?')->default(true),
            Select::make('igreja_id')
                ->relationship('igreja', 'name')
                ->searchable()
                ->nullable()
                ->label('Igreja responsável'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('igreja.name')->label('Igreja')->searchable()->sortable(),
            TextColumn::make('nome')->searchable()->sortable(),
            TextColumn::make('descricao')->limit(50),
            BadgeColumn::make('politica_ingresso')
                ->colors([
                    'success' => 'aberto',
                    'warning' => 'restrito',
                ]),
            BooleanColumn::make('ativo')->label('Ativo'),
            TextColumn::make('data_fundacao')->date(),
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
            'index' => Pages\ListMinisterios::route('/'),
            'create' => Pages\CreateMinisterio::route('/create'),
            'edit' => Pages\EditMinisterio::route('/{record}/edit'),
        ];
    }
}
