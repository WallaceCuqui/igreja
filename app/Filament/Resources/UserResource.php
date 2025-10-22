<?php

namespace App\Filament\Resources;

use Illuminate\Support\Facades\Log;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Resources\Resource;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;


use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

//Para verificar as permissões
use App\Filament\Resources\Traits\HasModuleAccess;

class UserResource extends Resource
{
    

    // só define qual módulo será usado
    // o nome do módulo deve ser igual ao definido em config/modules.php
    use HasModuleAccess;
    protected static string $moduleForAccess = 'users';

    protected static ?string $navigationLabel = 'Users';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Administração';


    public static function form(Form $form): Form
    {
        $user = auth()->user();
        /*\Log::info('🔹 Usuário logado no UserResource', [
            'id' => $user?->id,
            'email' => $user?->email,
            'roles' => $user?->roles?->pluck('name')->toArray(),
        ]);*/

        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Nome'),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->label('E-mail'),

                TextInput::make('password')
                    ->password()
                    ->required(fn ($record) => !$record)
                    ->label('Senha')
                    ->dehydrateStateUsing(fn ($state) => bcrypt($state)),

                Select::make('roles')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->label('Grupo Acesso')
                    ->preload(),
            ]);
        
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();
        /*\Log::info('🔹 Usuário logado no UserResource', [
            'id' => $user?->id,
            'email' => $user?->email,
            'roles' => $user?->roles?->pluck('name')->toArray(),
        ]);*/

        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('email')->sortable()->searchable(),
                TextColumn::make('roles.name')
                    ->label('Papéis')
                    ->sortable()
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        $user = auth()->user();
        /*\Log::info('🔹 Usuário logado no UserResource', [
            'id' => $user?->id,
            'email' => $user?->email,
            'roles' => $user?->roles?->pluck('name')->toArray(),
        ]);*/

        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
