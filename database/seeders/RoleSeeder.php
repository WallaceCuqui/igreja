<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $super = Role::firstOrCreate(['name' => 'superuser']);
        $staff = Role::firstOrCreate(['name' => 'staff']);

        // Exemplo de permissões:
        Permission::firstOrCreate(['name' => 'manage users']);
        Permission::firstOrCreate(['name' => 'view reports']);

        $super->givePermissionTo(Permission::all()); // Superuser tem tudo
    }
}
