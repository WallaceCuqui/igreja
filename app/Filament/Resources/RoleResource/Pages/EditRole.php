<?php

namespace App\Filament\Resources\RoleResource\Pages;

use Illuminate\Support\Facades\Log;
use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Permission;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['permissions']); // remove antes de atualizar
        return $data;
    }

    public function afterSave(): void
    {
        // Pega o estado do formulário
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
