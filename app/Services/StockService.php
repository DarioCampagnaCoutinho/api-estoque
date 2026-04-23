<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Retorna o estoque de um produto ou cria um zerado.
     */
    public function getOrCreateStock(Product $product): Stock
    {
        return Stock::firstOrCreate(
            ['product_id' => $product->id],
            ['quantity' => 0, 'min_quantity' => 0]
        );
    }

    /**
     * Realiza uma movimentação de estoque de forma atômica.
     *
     * @param array{
     *   product_id: int,
     *   type: 'in'|'out'|'adjustment',
     *   quantity: float,
     *   unit_cost?: float|null,
     *   reason?: string|null,
     *   reference?: string|null,
     *   notes?: string|null,
     * } $data
     * @throws \DomainException quando o estoque for insuficiente para saída
     */
    public function movement(array $data, int $userId): StockMovement
    {
        return DB::transaction(function () use ($data, $userId) {
            // Lock para evitar race condition
            $stock = Stock::lockForUpdate()->firstOrCreate(
                ['product_id' => $data['product_id']],
                ['quantity' => 0, 'min_quantity' => 0]
            );

            $before = (float) $stock->quantity;
            $qty    = (float) $data['quantity'];

            $after = match ($data['type']) {
                'in'         => $before + $qty,
                'out'        => $before - $qty,
                'adjustment' => $qty,
            };

            if ($after < 0) {
                throw new \DomainException(
                    "Estoque insuficiente. Quantidade atual: {$before}, solicitada para saída: {$qty}."
                );
            }

            $stock->update(['quantity' => $after]);

            return StockMovement::create([
                'product_id'      => $data['product_id'],
                'user_id'         => $userId,
                'type'            => $data['type'],
                'quantity'        => $qty,
                'quantity_before' => $before,
                'quantity_after'  => $after,
                'unit_cost'       => $data['unit_cost'] ?? null,
                'reason'          => $data['reason'] ?? null,
                'reference'       => $data['reference'] ?? null,
                'notes'           => $data['notes'] ?? null,
            ]);
        });
    }
}
