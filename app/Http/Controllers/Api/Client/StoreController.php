<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
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
            'email' => 'required|email',
            'tva' => 'required|string',
            'company' => 'required|string',
            'adresse' => 'required|string',
            'code_postal' => 'required|integer',
            'ville' => 'required|string',
        ]);

        $client = Client::create($validated);

        return response()->json([
            'success' => true,
            'client' => $client,
        ]);
    }
}
