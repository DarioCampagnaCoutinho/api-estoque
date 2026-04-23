<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = User::with('roles', 'directPermissions')
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%"))
            ->paginate($request->get('per_page', 20));

        return response()->json($users);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users'],
            'password' => ['required', Password::defaults()],
            'roles'    => ['nullable', 'array'],
            'roles.*'  => ['exists:roles,id'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        if (!empty($data['roles'])) {
            $user->roles()->sync($data['roles']);
        }

        return response()->json($user->load('roles'), 201);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json(
            $user->load('roles', 'directPermissions')
                 ->append([])
                 ->setRelation('all_permissions', $user->allPermissions()->values())
        );
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'name'     => ['sometimes', 'string', 'max:255'],
            'email'    => ['sometimes', 'email', 'unique:users,email,' . $user->id],
            'password' => ['sometimes', Password::defaults()],
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json($user->fresh('roles', 'directPermissions'));
    }

    public function destroy(User $user): JsonResponse
    {
        if ($user->id === request()->user()->id) {
            return response()->json(['message' => 'Não é possível remover o próprio usuário.'], 422);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => 'Usuário removido com sucesso.']);
    }

    /**
     * Sincroniza os grupos (roles) de um usuário.
     */
    public function syncRoles(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'roles'   => ['required', 'array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $user->roles()->sync($request->roles);

        return response()->json([
            'user'            => $user->load('roles'),
            'all_permissions' => $user->allPermissions()->values(),
        ]);
    }

    /**
     * Sincroniza permissões individuais de um usuário.
     */
    public function syncPermissions(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'permissions'   => ['required', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $user->directPermissions()->sync($request->permissions);

        return response()->json([
            'user'               => $user->load('roles'),
            'direct_permissions' => $user->directPermissions,
            'all_permissions'    => $user->allPermissions()->values(),
        ]);
    }
}
