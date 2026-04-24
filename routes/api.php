<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ─── Autenticação pública ────────────────────────────────────────────────────
Route::get('/health', HealthController::class);
Route::get('/auth/debug', function (\Illuminate\Http\Request $request) {
    return response()->json([
        'has_authorization_header' => $request->headers->has('authorization'),
        'authorization_prefix' => str($request->header('authorization', ''))->before(' ')->toString(),
        'has_bearer_token' => $request->bearerToken() !== null,
    ]);
});
Route::post('/auth/login', [AuthController::class, 'login']);

// ─── Rotas protegidas (Sanctum) ──────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);

    // ── Usuários ─────────────────────────────────────────────────────────────
    Route::middleware('permission:users.view')->group(function () {
        Route::get('/users',        [UserController::class, 'index']);
        Route::get('/users/{user}', [UserController::class, 'show']);
    });
    Route::middleware('permission:users.create')->post('/users', [UserController::class, 'store']);
    Route::middleware('permission:users.edit')->put('/users/{user}', [UserController::class, 'update']);
    Route::middleware('permission:users.delete')->delete('/users/{user}', [UserController::class, 'destroy']);
    Route::middleware('permission:users.manage-roles')->put('/users/{user}/roles', [UserController::class, 'syncRoles']);
    Route::middleware('permission:users.manage-permissions')->put('/users/{user}/permissions', [UserController::class, 'syncPermissions']);

    // ── Roles (grupos) ───────────────────────────────────────────────────────
    Route::middleware('permission:roles.view')->get('/roles', [RoleController::class, 'index']);
    Route::middleware('permission:roles.view')->get('/roles/{role}', [RoleController::class, 'show']);
    Route::middleware('permission:roles.create')->post('/roles', [RoleController::class, 'store']);
    Route::middleware('permission:roles.edit')->put('/roles/{role}', [RoleController::class, 'update']);
    Route::middleware('permission:roles.delete')->delete('/roles/{role}', [RoleController::class, 'destroy']);
    Route::middleware('permission:roles.edit')->put('/roles/{role}/permissions', [RoleController::class, 'syncPermissions']);

    // ── Permissões ───────────────────────────────────────────────────────────
    Route::middleware('permission:permissions.view')->get('/permissions', [PermissionController::class, 'index']);
    Route::middleware('permission:permissions.create')->post('/permissions', [PermissionController::class, 'store']);
    Route::middleware('permission:permissions.edit')->put('/permissions/{permission}', [PermissionController::class, 'update']);
    Route::middleware('permission:permissions.delete')->delete('/permissions/{permission}', [PermissionController::class, 'destroy']);

    // ── Produtos ─────────────────────────────────────────────────────────────
    Route::middleware('permission:products.view')->group(function () {
        Route::get('/products',           [ProductController::class, 'index']);
        Route::get('/products/{product}', [ProductController::class, 'show']);
    });
    Route::middleware('permission:products.create')->post('/products', [ProductController::class, 'store']);
    Route::middleware('permission:products.edit')->put('/products/{product}', [ProductController::class, 'update']);
    Route::middleware('permission:products.delete')->delete('/products/{product}', [ProductController::class, 'destroy']);

    // ── Estoque ──────────────────────────────────────────────────────────────
    Route::middleware('permission:stock.view')->group(function () {
        Route::get('/stock',                              [StockController::class, 'index']);
        Route::get('/stock/movements',                    [StockController::class, 'allMovements']);
        Route::get('/stock/product/{product}',            [StockController::class, 'show']);
        Route::get('/stock/product/{product}/movements',  [StockController::class, 'movements']);
    });
    Route::middleware('permission:stock.move')->post('/stock/movement', [StockController::class, 'movement']);
});
