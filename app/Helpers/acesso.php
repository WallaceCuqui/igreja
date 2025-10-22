<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

if (! function_exists('acesso')) {
    function acesso(string $module): array
    {
        $user = Auth::user();

        if (!$user) {
            Log::warning('🔒 Nenhum usuário autenticado.');
            return [];
        }

        // Pega o primeiro papel (role) do usuário
        $role = $user->roles()->first();

        if (!$role) {
            Log::warning('⚠️ Usuário sem grupo definido.', ['user_id' => $user->id]);
            return [];
        }

        // Busca as permissões desse papel (Role)
        $permissions = $role->permissions()->pluck('name')->toArray();

        Log::info('🔹 Permissões do grupo', [
            'user_id' => $user->id,
            'role' => $role->name,
            'permissions' => $permissions,
        ]);

        // Filtra só as permissões referentes ao módulo passado (ex: users.view, users.create...)
        $filtered = array_filter($permissions, fn($perm) => str_starts_with($perm, "{$module}."));

        Log::info('✅ Permissões do módulo filtradas', [
            'module' => $module,
            'filtered' => $filtered,
        ]);

        // Retorna apenas as ações (ex: ['view', 'edit'])
        return array_map(fn($perm) => explode('.', $perm)[1] ?? null, $filtered);
    }
}
