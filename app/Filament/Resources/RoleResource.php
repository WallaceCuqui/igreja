<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Grupos';
    protected static ?string $navigationGroup = 'Administração';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                // Campo para o nome do grupo
                TextInput::make('name')
                    ->label('Nome do Grupo')
                    ->required()
                    ->maxLength(50),

                // CheckboxList para selecionar permissões
                CheckboxList::make('permissions')
                    ->label('Permissões')
                    ->options([
                        'view' => 'Ver',
                        'create' => 'Criar',
                        'edit' => 'Editar',
                        'delete' => 'Deletar',
                    ])
                    ->columns(4) // mostra em 4 colunas
                    ->saveRelationships(), // importante para salvar no Spatie
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nome do Grupo')->sortable()->searchable(),
                TextColumn::make('permissions')->label('Permissões')->getStateUsing(function ($record) {
                    return $record->permissions->pluck('name')->join(', ');
                }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
