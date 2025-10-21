<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Permission;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
{
    // apenas filtra dados que vão para a tabela
    return [
        'name' => $data['name'],
        'permissions' => $data['permissions'] ?? [],
    ];
}

protected function afterCreate(): void
{
    $permissions = $this->form->getState()['permissions'] ?? [];
    $flattened = [];
    foreach ($permissions as $module => $actions) {
        foreach ($actions as $action => $value) {
            if ($value) {
                $flattened[] = "{$module}.{$action}";
            }
        }
    }

    Log::info('🔹 Permissões a salvar após criar', ['permissions' => $flattened]);

    foreach ($flattened as $permissionName) {
        Permission::firstOrCreate([
            'name' => $permissionName,
            'guard_name' => 'web',
        ]);
    }

    $this->record->syncPermissions($flattened);
}


}
