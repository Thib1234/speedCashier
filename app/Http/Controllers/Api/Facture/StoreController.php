<?php

namespace App\Http\Controllers\Api\Facture;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Facture;
use Illuminate\Http\JsonResponse;

class StoreController extends Controller
{
    /**
     * Handle the incoming request.
     */

	public function __invoke(Request $request): JsonResponse
    {
		// return response()->json($request);
        $validated = $request->validate([
            'id' => 'required|integer',
        ]);

        $facture = Facture::create($validated);

        return response()->json([
            'success' => true,
            'facture' => $facture,
        ]);
    }
}
