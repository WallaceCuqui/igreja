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
        unset($data['permissions']); // remove o array de checkboxes antes de salvar na tabela roles
        return $data;
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

        // Cria as permissões se não existirem
        foreach ($flattened as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        // Sincroniza com o role
        $this->record->syncPermissions($flattened);

    }


}
