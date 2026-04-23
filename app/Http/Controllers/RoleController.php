<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Role::with('permissions')->withCount('users')->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'unique:roles'],
            'description'    => ['nullable', 'string'],
            'permissions'    => ['nullable', 'array'],
            'permissions.*'  => ['exists:permissions,id'],
        ]);

        $role = Role::create([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        if (!empty($data['permissions'])) {
            $role->permissions()->sync($data['permissions']);
        }

        return response()->json($role->load('permissions'), 201);
    }

    public function show(Role $role): JsonResponse
    {
        return response()->json(
            $role->load('permissions')->loadCount('users')
        );
    }

    public function update(Request $request, Role $role): JsonResponse
    {
        $data = $request->validate([
            'name'        => ['sometimes', 'string', 'unique:roles,name,' . $role->id],
            'description' => ['nullable', 'string'],
        ]);

        $role->update($data);

        return response()->json($role->fresh('permissions'));
    }

    public function destroy(Role $role): JsonResponse
    {
        $role->users()->detach();
        $role->permissions()->detach();
        $role->delete();

        return response()->json(['message' => 'Grupo removido com sucesso.']);
    }

    /**
     * Sincroniza permissões de um grupo.
     */
    public function syncPermissions(Request $request, Role $role): JsonResponse
    {
        $request->validate([
            'permissions'   => ['required', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->permissions()->sync($request->permissions);

        return response()->json($role->load('permissions'));
    }
}
