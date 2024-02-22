<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class StoreController extends Controller
{
    /**
     * Handle the incoming request.
     */

	public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|integer',
            'stock' => 'required|integer',
        ]);

        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'product' => $product,
        ]);
    }
}
