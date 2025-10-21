<?php

namespace App\Filament\Resources\Traits;

use function App\Helpers\acesso;

trait HasModuleAccess
{
    /**
     * Define o módulo que será usado para verificar permissões
     */
    protected static string $moduleForAccess = '';

    public static function canViewAny(): bool
    {
        $perms = acesso(static::$moduleForAccess);
        return $perms['view'] ?? false;
    }

    public static function canCreate(): bool
    {
        $perms = acesso(static::$moduleForAccess);
        return $perms['create'] ?? false;
    }

    public static function canEdit($record): bool
    {
        $perms = acesso(static::$moduleForAccess);
        return $perms['edit'] ?? false;
    }

    public static function canDelete($record): bool
    {
        $perms = acesso(static::$moduleForAccess);
        return $perms['delete'] ?? false;
    }
}
