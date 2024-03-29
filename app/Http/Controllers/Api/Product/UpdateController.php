<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class UpdateController extends Controller
{
    /**
     * Handle the incoming request.
     */

	public function __invoke(Request $request, Product $product)
	{
		$validatedData = $request->validate([
			'name' => 'required|string|max:255',
			'stock' => 'nullable|integer',
			'purchase_price' => 'nullable|numeric',
			'price' => 'required|numeric',
		]);

		$product->update($validatedData);

		return response()->json(['message' => 'Product updated successfully']);
	}
}
