<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Representa o usuário autenticável do sistema.
 *
 * Além dos dados básicos de acesso, este model centraliza a associação do usuário
 * com grupos (roles) e permissões diretas, permitindo resolver o que ele pode
 * acessar tanto por vínculo individual quanto por herança via grupos.
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Retorna os grupos aos quais o usuário pertence.
     *
     * Esses grupos são usados para organizar perfis de acesso e herdar permissões
     * de forma indireta, sem precisar vincular tudo manualmente ao usuário.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    /**
     * Retorna as permissões atribuídas diretamente ao usuário.
     *
     * Esse relacionamento complementa as permissões herdadas por grupos e atende
     * cenários em que um acesso precisa ser concedido de forma específica.
     */
    public function directPermissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permission');
    }

    /**
     * Consolida todas as permissões efetivas do usuário.
     *
     * O resultado combina as permissões diretas com as permissões herdadas dos
     * grupos vinculados, removendo duplicidades para facilitar validações de acesso.
     */
    public function allPermissions(): \Illuminate\Support\Collection
    {
        $fromRoles = $this->roles()
            ->with('permissions')
            ->get()
            ->flatMap(fn($role) => $role->permissions);

        return $this->directPermissions
            ->merge($fromRoles)
            ->unique('id')
            ->values();
    }

    /**
     * Verifica se o usuário possui uma permissão específica.
     *
     * A checagem considera tanto permissões diretas quanto permissões herdadas
     * dos grupos associados ao usuário.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->allPermissions()->contains('name', $permission);
    }

    /**
     * Verifica se o usuário está associado a um grupo específico.
     *
     * Esse método é útil para regras de autorização baseadas em perfil, cargo
     * ou responsabilidade dentro da aplicação.
     */
    public function hasRole(string $role): bool
    {
        return $this->roles->contains('name', $role);
    }
}
