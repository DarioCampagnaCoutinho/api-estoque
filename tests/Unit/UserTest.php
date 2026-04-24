<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('hashes the user password automatically', function () {
    $user = User::create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => 'password',
    ]);

    expect($user->password)->not->toBe('password');
    expect(Hash::check('password', $user->password))->toBeTrue();
});

it('checks if user has a role', function () {
    $user = User::create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => 'password',
    ]);

    $role = Role::create([
        'name' => 'admin',
        'description' => 'Administrador',
    ]);

    $user->roles()->attach($role);

    expect($user->fresh()->hasRole('admin'))->toBeTrue();
    expect($user->fresh()->hasRole('manager'))->toBeFalse();
});

it('checks direct permissions', function () {
    $user = User::create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => 'password',
    ]);

    $permission = Permission::create([
        'name' => 'products.create',
        'description' => 'Criar produtos',
        'group' => 'products',
    ]);

    $user->directPermissions()->attach($permission);

    expect($user->fresh()->hasPermission('products.create'))->toBeTrue();
    expect($user->fresh()->hasPermission('products.delete'))->toBeFalse();
});

it('checks permissions inherited from roles', function () {
    $user = User::create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => 'password',
    ]);

    $role = Role::create([
        'name' => 'manager',
        'description' => 'Gerente',
    ]);

    $permission = Permission::create([
        'name' => 'stock.move',
        'description' => 'Movimentar estoque',
        'group' => 'stock',
    ]);

    $role->permissions()->attach($permission);
    $user->roles()->attach($role);

    expect($user->fresh()->hasPermission('stock.move'))->toBeTrue();
});

it('deduplicates permissions granted directly and through roles', function () {
    $user = User::create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => 'password',
    ]);

    $role = Role::create([
        'name' => 'manager',
        'description' => 'Gerente',
    ]);

    $permission = Permission::create([
        'name' => 'products.view',
        'description' => 'Visualizar produtos',
        'group' => 'products',
    ]);

    $role->permissions()->attach($permission);
    $user->roles()->attach($role);
    $user->directPermissions()->attach($permission);

    expect($user->fresh()->allPermissions())->toHaveCount(1);
});
