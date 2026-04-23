<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Lista todas as permissões agrupadas por group.
     */
    public function index(): JsonResponse
    {
        $permissions = Permission::all()->groupBy('group');
        return response()->json($permissions);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'unique:permissions'],
            'description' => ['nullable', 'string'],
            'group'       => ['nullable', 'string', 'max:50'],
        ]);

        return response()->json(Permission::create($data), 201);
    }

    public function update(Request $request, Permission $permission): JsonResponse
    {
        $data = $request->validate([
            'name'        => ['sometimes', 'string', 'unique:permissions,name,' . $permission->id],
            'description' => ['nullable', 'string'],
            'group'       => ['nullable', 'string', 'max:50'],
        ]);

        $permission->update($data);

        return response()->json($permission->fresh());
    }

    public function destroy(Permission $permission): JsonResponse
    {
        $permission->roles()->detach();
        $permission->users()->detach();
        $permission->delete();

        return response()->json(['message' => 'Permissão removida com sucesso.']);
    }
}
