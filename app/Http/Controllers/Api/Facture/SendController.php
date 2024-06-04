<?php

namespace App\Http\Controllers\Api\Facture;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Facture;
use App\Models\Client;
use Illuminate\Http\JsonResponse;

class SendController extends Controller
{
	public function __invoke(Request $request): JsonResponse
{
    $validated = $request->validate([
        'id' => 'required|integer',
    ]);

    $facture = Facture::with('client', 'sale', 'sale.products')->findOrFail($validated['id']);

    return response()->json([
        'facture' => $facture,
    ]);

    $client = Client::find($validated['client_id']);
    $sale = Sale::find($validated['sale_id']);

    if ($client && $sale) {
        $facture = new Facture;
        $facture->client_id = $client->id;
        $facture->sale_id = $sale->id;
        $facture->date_facture = now()->toDateString();
        // Ajoutez ici d'autres champs si nécessaire
        $facture->save();

        return response()->json([
            'success' => true,
            'facture' => $facture,
        ]);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Client ou vente non trouvé',
        ]);
    }
}

}
