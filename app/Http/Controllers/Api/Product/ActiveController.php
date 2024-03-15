<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ActiveController extends Controller
{
    /**
     * Handle the incoming request.
     */

	public function __invoke(Request $request, Product $product)
	{
		$validatedData = $request->validate([
			'active' => 'boolean',
		]);

		$product->update($validatedData);

		return response()->json(['message' => 'issou']);
	}
}
