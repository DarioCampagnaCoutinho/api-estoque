<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Representa uma permissão individual de acesso.
 *
 * Cada permissão descreve uma capacidade específica do sistema e pode ser
 * associada diretamente a usuários ou agrupada em roles para reutilização.
 */
class Permission extends Model
{
    protected $fillable = [
        'name',
        'description',
        'group',
    ];

    /**
     * Retorna os grupos que concedem esta permissão.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    /**
     * Retorna os usuários que receberam esta permissão diretamente.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permission');
    }
}
