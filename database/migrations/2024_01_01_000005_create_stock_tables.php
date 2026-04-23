<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Estoque atual por produto (saldo)
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 10, 3)->default(0)->comment('Quantidade atual em estoque');
            $table->decimal('min_quantity', 10, 3)->default(0)->comment('Estoque mínimo para alerta');
            $table->timestamps();
        });

        // Histórico de movimentações
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->comment('Responsável pela movimentação');
            $table->enum('type', ['in', 'out', 'adjustment'])->comment('in=entrada, out=saída, adjustment=ajuste');
            $table->decimal('quantity', 10, 3)->comment('Quantidade movimentada');
            $table->decimal('quantity_before', 10, 3)->comment('Quantidade antes da movimentação');
            $table->decimal('quantity_after', 10, 3)->comment('Quantidade após a movimentação');
            $table->decimal('unit_cost', 10, 2)->nullable()->comment('Custo unitário na movimentação');
            $table->string('reason')->nullable()->comment('Motivo da movimentação');
            $table->string('reference')->nullable()->comment('Referência: NF, pedido, etc.');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'created_at']);
            $table->index(['type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('stocks');
    }
};
