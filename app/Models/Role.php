<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Representa um grupo de acesso do sistema.
 *
 * Roles agrupam permissões em perfis reutilizáveis, facilitando a gestão de
 * autorização ao vincular usuários a conjuntos prontos de capacidades.
 */
class Role extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Retorna as permissões associadas a este grupo.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /**
     * Retorna os usuários vinculados a este grupo.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role');
    }
}
