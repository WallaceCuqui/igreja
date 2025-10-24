<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

//Para verificar as permissões
use App\Filament\Resources\Traits\HasModuleAccess;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;

class RoleResource extends Resource
{
    
    // só define qual módulo será usado
    // o nome do módulo deve ser igual ao definido em config/modules.php
    use HasModuleAccess;
    protected static string $moduleForAccess = 'roles';

    protected static ?string $model = Role::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Grupos Permissões';
    protected static ?string $navigationGroup = 'Administração';

    public static function form(Form $form): Form
    {
        $modules = config('modules.modules');
        $actions = ['view' => 'Ver', 'create' => 'Criar', 'edit' => 'Editar', 'delete' => 'Excluir'];

        $fieldsets = [];

        foreach ($modules as $moduleKey => $moduleLabel) {
            $checkboxes = [];

            foreach ($actions as $actionKey => $actionLabel) {
                $permissionName = "{$moduleKey}.{$actionKey}";

                $checkboxes[] = Checkbox::make("permissions.{$permissionName}")
                    ->label($actionLabel)
                    ->afterStateHydrated(function ($state, callable $set, $record = null) use ($permissionName) {
                        if ($record && Permission::where('name', $permissionName)->exists()) {
                            if ($record->hasPermissionTo($permissionName)) {
                                $set('permissions.' . $permissionName, true);
                            }
                        }
                    });
            }

            $fieldsets[] = Fieldset::make($moduleLabel)
                ->schema($checkboxes)
                ->columns(4);
        }

        return $form
            ->schema(array_merge([
                TextInput::make('name')
                    ->label('Nome do Grupo')
                    ->required()
                    ->maxLength(50),
            ], $fieldsets));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->sortable()
                    ->searchable(),
            ])
            ->actions([
                // Botão de editar (caso queira adicionar no futuro)
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => static::canEdit($record)),

                // Botão de deletar, só aparece se tiver permissão
                DeleteAction::make()
                    ->visible(fn ($record) => static::canDelete($record))
                    ->authorize(fn ($record) => static::canDelete($record)), // reforço backend
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->visible(fn () => static::checkAccess('delete'))
                    ->authorize(fn () => static::checkAccess('delete')), // reforço backend
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
