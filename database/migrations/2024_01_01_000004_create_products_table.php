<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique()->comment('Código único do produto');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->comment('Preço de venda');
            $table->decimal('cost', 10, 2)->nullable()->comment('Custo do produto');
            $table->string('unit', 10)->default('un')->comment('un, kg, lt, cx...');
            $table->boolean('active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
