<?php

namespace App\Http\Controllers\Api\Sale;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;

class DeleteSaleController extends Controller
{
    public function __invoke(Request $request, $saleId)
    {
        try {
            $sale = Sale::findOrFail($saleId);
            $sale->delete();

            return response()->json(['message' => 'Vente supprimée avec succès', 'sale_id' => $saleId], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Erreur lors de la suppression de la vente', 'message' => 'La vente avec l\'ID spécifié n\'a pas été trouvée'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la suppression de la vente', 'message' => $e->getMessage()], 500);
        }
    }
}
