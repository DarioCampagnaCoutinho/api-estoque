<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Permissões ────────────────────────────────────────────────────────
        $permissions = [
            // Usuários
            ['name' => 'users.view',               'group' => 'users',       'description' => 'Visualizar usuários'],
            ['name' => 'users.create',             'group' => 'users',       'description' => 'Criar usuários'],
            ['name' => 'users.edit',               'group' => 'users',       'description' => 'Editar usuários'],
            ['name' => 'users.delete',             'group' => 'users',       'description' => 'Excluir usuários'],
            ['name' => 'users.manage-roles',       'group' => 'users',       'description' => 'Gerenciar grupos dos usuários'],
            ['name' => 'users.manage-permissions', 'group' => 'users',       'description' => 'Gerenciar permissões individuais'],
            // Grupos
            ['name' => 'roles.view',               'group' => 'roles',       'description' => 'Visualizar grupos'],
            ['name' => 'roles.create',             'group' => 'roles',       'description' => 'Criar grupos'],
            ['name' => 'roles.edit',               'group' => 'roles',       'description' => 'Editar grupos'],
            ['name' => 'roles.delete',             'group' => 'roles',       'description' => 'Excluir grupos'],
            // Permissões
            ['name' => 'permissions.view',         'group' => 'permissions', 'description' => 'Visualizar permissões'],
            ['name' => 'permissions.create',       'group' => 'permissions', 'description' => 'Criar permissões'],
            ['name' => 'permissions.edit',         'group' => 'permissions', 'description' => 'Editar permissões'],
            ['name' => 'permissions.delete',       'group' => 'permissions', 'description' => 'Excluir permissões'],
            // Produtos
            ['name' => 'products.view',            'group' => 'products',    'description' => 'Visualizar produtos'],
            ['name' => 'products.create',          'group' => 'products',    'description' => 'Criar produtos'],
            ['name' => 'products.edit',            'group' => 'products',    'description' => 'Editar produtos'],
            ['name' => 'products.delete',          'group' => 'products',    'description' => 'Excluir produtos'],
            // Estoque
            ['name' => 'stock.view',               'group' => 'stock',       'description' => 'Visualizar estoque e movimentações'],
            ['name' => 'stock.move',               'group' => 'stock',       'description' => 'Realizar movimentações de estoque'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm['name']], $perm);
        }

        // ── Role: Admin — acesso total ────────────────────────────────────────
        $admin = Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administrador com acesso total ao sistema']
        );
        $admin->permissions()->sync(Permission::all()->pluck('id'));

        // ── Role: Manager — produtos e estoque ───────────────────────────────
        $manager = Role::firstOrCreate(
            ['name' => 'manager'],
            ['description' => 'Gerente de produtos e estoque']
        );
        $manager->permissions()->sync(
            Permission::whereIn('group', ['products', 'stock'])->pluck('id')
        );

        // ── Role: Viewer — somente leitura ───────────────────────────────────
        $viewer = Role::firstOrCreate(
            ['name' => 'viewer'],
            ['description' => 'Acesso somente leitura']
        );
        $viewer->permissions()->sync(
            Permission::where('name', 'like', '%.view')->pluck('id')
        );

        // ── Usuário admin padrão ──────────────────────────────────────────────
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('password'),
            ]
        );
        $user->roles()->sync([$admin->id]);

        // ── Usuário manager de exemplo ────────────────────────────────────────
        $manager_user = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name'     => 'Manager',
                'password' => Hash::make('password'),
            ]
        );
        $manager_user->roles()->sync([$manager->id]);

        $this->command->info('Seeder concluído!');
        $this->command->info('Admin:   admin@example.com   / password');
        $this->command->info('Manager: manager@example.com / password');
    }
}
