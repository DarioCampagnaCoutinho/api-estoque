<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Verifica se o usuário autenticado possui pelo menos uma das permissões exigidas.
     * Uso na rota: middleware('permission:products.create,products.edit')
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        foreach ($permissions as $permission) {
            if ($request->user()->hasPermission($permission)) {
                return $next($request);
            }
        }

        return response()->json([
            'message' => 'Acesso negado. Permissão necessária: ' . implode(' ou ', $permissions),
        ], 403);
    }
}
