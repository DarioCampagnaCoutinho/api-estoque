<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Registra cada movimentação realizada no estoque de um produto.
 *
 * Este model funciona como histórico operacional, armazenando quem executou a
 * ação, o tipo de movimentação, as quantidades antes e depois da alteração e
 * informações de apoio para auditoria e rastreabilidade.
 */
class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'quantity_before',
        'quantity_after',
        'unit_cost',
        'reason',
        'reference',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity'        => 'decimal:3',
            'quantity_before' => 'decimal:3',
            'quantity_after'  => 'decimal:3',
            'unit_cost'       => 'decimal:2',
        ];
    }

    /**
     * Retorna o produto impactado pela movimentação.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Retorna o usuário responsável por registrar a movimentação.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
