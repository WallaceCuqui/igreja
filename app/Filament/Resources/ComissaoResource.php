<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComissaoResource\Pages;
use App\Models\Comissao;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\{Select, TextInput, Textarea, DatePicker, Toggle};
use Filament\Tables\Columns\{TextColumn, BooleanColumn};
use Filament\Tables\Actions\{EditAction};

//Para verificar as permissões
use App\Filament\Resources\Traits\HasModuleAccess;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;



class ComissaoResource extends Resource
{
    // só define qual módulo será usado
    // o nome do módulo deve ser igual ao definido em config/modules.php
    use HasModuleAccess;
    protected static string $moduleForAccess = 'comissoes';

    protected static ?string $model = Comissao::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationLabel = 'Comissões';
    protected static ?string $pluralLabel = 'Comissões';
    protected static ?string $slug = 'comissoes';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('ministerio_id')
                ->relationship('ministerio', 'nome')
                ->required()
                ->label('Ministério'),

            Select::make('membro_id')
                ->relationship('membro', 'name')
                ->required()
                ->label('Membro'),

            TextInput::make('funcao')->required()->maxLength(255),
            Textarea::make('observacoes')->rows(3)->nullable(),
            DatePicker::make('data_entrada')->label('Data de Entrada'),
            DatePicker::make('data_saida')->label('Data de Saída'),
            Toggle::make('ativo')->label('Está ativo?')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('ministerio.nome')->label('Ministério')->sortable()->searchable(),
            TextColumn::make('membro.name')->label('Membro')->sortable()->searchable(),
            TextColumn::make('funcao')->sortable()->searchable(),
            TextColumn::make('data_entrada')->label('Entrada')->date(),
            TextColumn::make('data_saida')->label('Saída')->date(),
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
            'index' => Pages\ListComissao::route('/'),
            'create' => Pages\CreateComissao::route('/create'),
            'edit' => Pages\EditComissao::route('/{record}/edit'),
        ];
    }
}
