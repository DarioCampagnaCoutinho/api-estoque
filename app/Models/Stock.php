<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Representa o saldo atual de estoque de um produto.
 *
 * Este model guarda a quantidade disponível e o limite mínimo configurado para
 * reposição, servindo como referência para consultas rápidas sobre a situação
 * atual do item no estoque.
 */
class Stock extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'min_quantity',
    ];

    protected function casts(): array
    {
        return [
            'quantity'     => 'decimal:3',
            'min_quantity' => 'decimal:3',
        ];
    }

    /**
     * Retorna o produto ao qual este estoque pertence.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Indica se o estoque está no nível mínimo ou abaixo dele.
     *
     * Esse método ajuda a identificar rapidamente itens que precisam de atenção
     * para reposição, evitando ruptura no abastecimento.
     */
    public function isLow(): bool
    {
        return $this->quantity <= $this->min_quantity;
    }
}
