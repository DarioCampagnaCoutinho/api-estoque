<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Permissões atômicas do sistema
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('ex: products.create');
            $table->string('description')->nullable();
            $table->string('group')->nullable()->comment('ex: products, stock, users');
            $table->timestamps();
        });

        // Grupos de permissões (roles)
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('ex: admin, manager, viewer');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Permissões atribuídas a cada grupo
        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->unique(['role_id', 'permission_id']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
    }
};
