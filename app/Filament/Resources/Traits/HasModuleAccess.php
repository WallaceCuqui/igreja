<?php

namespace App\Filament\Resources\Traits;

use Illuminate\Support\Facades\Log;
use App\Models\User;

trait HasModuleAccess
{
    public static function canViewAny(): bool
    {
        return static::checkAccess('view');
    }

    public static function canView($record): bool
    {
        return static::checkAccess('view');
    }

    public static function canCreate(): bool
    {
        return static::checkAccess('create');
    }

    public static function canEdit($record): bool
    {
        return static::checkAccess('edit');
    }

    public static function canDelete($record): bool
    {
        return static::checkAccess('delete');
    }

    /**
     * Faz a verificação de acesso usando o módulo definido no Resource.
     */
    protected static function checkAccess(string $action): bool
    {
        Log::info('🔐 Verificando acesso para ação: ' . $action);
        try {

            // Se houver apenas 1 usuário no banco, libera total
            if (User::count() === 1) {
                Log::info('🔓 Apenas 1 usuário no banco, acesso total liberado.');
                return true;
            }


            $module = static::$moduleForAccess ?? null;

            if (!$module) {
                Log::warning('⚠️ Nenhum módulo definido em $moduleForAccess no resource.');
                return false;
            }

            $permissions = acesso($module); // chama o helper global
            Log::info('🔍 Verificando permissão', [
                'module' => $module,
                'action' => $action,
                'permissions' => $permissions,
            ]);

            // Se o usuário tiver a ação específica no módulo, libera o acesso
            return in_array($action, $permissions, true);
        } catch (\Throwable $e) {
            Log::error('❌ Erro ao verificar acesso', [
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
