<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;

use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Tables\Columns\TextColumn;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Model;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Grupos de Acesso';
    protected static ?string $pluralModelLabel = 'Grupos de Acesso';
    protected static ?string $slug = 'grupos-de-acesso';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Nome do Grupo')
                ->required(),

            CheckboxList::make('permissions')
                ->label('Permissões')
                ->options(function () {
                    return Permission::all()->pluck('name', 'id');
                })
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Grupo'),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
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
