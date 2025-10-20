<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ShowUserPermissions extends Command
{
    // Nome do comando no terminal
    protected $signature = 'user:permissions {user_id}';

    // Descrição
    protected $description = 'Mostra todas as permissões de um usuário';

    public function handle()
    {
        $userId = $this->argument('user_id');

        $user = User::find($userId);

        if (! $user) {
            $this->error("Usuário ID {$userId} não encontrado.");
            return 1;
        }

        $roles = $user->roles->pluck('name')->toArray();
        $directPermissions = $user->getDirectPermissions()->pluck('name')->toArray();
        $rolePermissions = $user->getPermissionsViaRoles()->pluck('name')->toArray();
        $allPermissions = $user->getAllPermissions()->pluck('name')->toArray();

        $this->info("Usuário: {$user->name} ({$user->id})");
        $this->line("Roles: " . implode(', ', $roles));
        $this->line("Permissões diretas: " . implode(', ', $directPermissions));
        $this->line("Permissões via roles: " . implode(', ', $rolePermissions));
        $this->line("Todas permissões: " . implode(', ', $allPermissions));

        return 0;
    }
}
