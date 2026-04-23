<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Representa um produto cadastrado no catálogo da aplicação.
 *
 * O model concentra os dados comerciais e operacionais do item, como preço,
 * custo, unidade de medida e status, além de conectar o produto ao estoque,
 * ao histórico de movimentações e ao usuário que realizou o cadastro.
 */
class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
        'cost',
        'unit',
        'active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'price'  => 'decimal:2',
            'cost'   => 'decimal:2',
            'active' => 'boolean',
        ];
    }

    /**
     * Retorna o registro de estoque atual do produto.
     */
    public function stock()
    {
        return $this->hasOne(Stock::class);
    }

    /**
     * Retorna o histórico de movimentações de estoque do produto.
     */
    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Retorna o usuário que criou o cadastro do produto.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
