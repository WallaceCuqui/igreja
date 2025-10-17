<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Permission;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->record->syncPermissions(Permission::whereIn('id', $data['permissions'])->get());
        return $data;
    }
}
