<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $products = Product::with('stock')
            ->when($request->search, fn($q, $s) =>
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('sku', 'like', "%{$s}%")
            )
            ->when($request->has('active'), fn($q) =>
                $q->where('active', $request->boolean('active'))
            )
            ->paginate($request->get('per_page', 20));

        return response()->json($products);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = $request->validated();

        $product = Product::create(array_merge(
            $data,
            ['created_by' => $request->user()->id]
        ));

        // Cria registro de estoque zerado
        $product->stock()->create([
            'quantity'     => 0,
            'min_quantity' => $data['min_quantity'] ?? 0,
        ]);

        return response()->json($product->load('stock'), 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product->load('stock', 'creator'));
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());

        return response()->json($product->fresh('stock'));
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete(); // SoftDelete

        return response()->json(['message' => 'Produto removido com sucesso.']);
    }
}
