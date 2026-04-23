<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockMovementRequest;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function __construct(protected StockService $stockService) {}

    /**
     * Lista estoque de todos os produtos.
     * ?low=true para filtrar produtos abaixo do mínimo.
     */
    public function index(Request $request): JsonResponse
    {
        $stocks = Stock::with('product')
            ->when($request->boolean('low'), fn($q) =>
                $q->whereColumn('quantity', '<=', 'min_quantity')
            )
            ->paginate($request->get('per_page', 20));

        return response()->json($stocks);
    }

    /**
     * Estoque atual de um produto específico.
     */
    public function show(Product $product): JsonResponse
    {
        $stock = $this->stockService->getOrCreateStock($product);
        return response()->json($stock->load('product'));
    }

    /**
     * Registra uma movimentação de estoque.
     * type: in (entrada), out (saída), adjustment (ajuste direto).
     */
    public function movement(StockMovementRequest $request): JsonResponse
    {
        try {
            $movement = $this->stockService->movement(
                $request->validated(),
                $request->user()->id
            );

            return response()->json($movement->load('product', 'user'), 201);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Histórico de movimentações de um produto específico.
     */
    public function movements(Request $request, Product $product): JsonResponse
    {
        $movements = $product->movements()
            ->with('user:id,name,email')
            ->when($request->type, fn($q, $t) => $q->where('type', $t))
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 20));

        return response()->json($movements);
    }

    /**
     * Histórico geral de movimentações.
     */
    public function allMovements(Request $request): JsonResponse
    {
        $movements = StockMovement::with('product:id,name,sku', 'user:id,name,email')
            ->when($request->type, fn($q, $t) => $q->where('type', $t))
            ->when($request->product_id, fn($q, $id) => $q->where('product_id', $id))
            ->when($request->date_from, fn($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($request->date_to, fn($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 20));

        return response()->json($movements);
    }
}
