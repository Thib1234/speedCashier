<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class StoreController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'integer|nullable',
            'purchase_price' => 'numeric|nullable',
            'category_id' => 'integer',
        ]);

        // Calcul du prix HTVA
        $taux_de_TVA = 0.21;
        $validated['price_htva'] = round($validated['price'] / (1 + $taux_de_TVA), 2);

        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'product' => $product,
        ]);
    }
}
