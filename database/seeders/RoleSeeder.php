<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Cria as roles básicas da plataforma, se ainda não existirem
        Role::firstOrCreate(['name' => 'user',      'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'bar_owner', 'guard_name' => 'web']);

        $this->command->info('Roles criadas: user, bar_owner');
    }
}