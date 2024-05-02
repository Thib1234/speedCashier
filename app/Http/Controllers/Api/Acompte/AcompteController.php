<?php

namespace App\Http\Controllers\Api\Acompte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Acompte;
use App\Models\Sale;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

class AcompteController extends Controller
{
	public function index()
	{
		$acomptes = Acompte::where('status', 'en attente')->get();
		return response()->json($acomptes);
	}

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'montant' => 'required|numeric|min:0',
            'product_id' => 'required|exists:products,id'
        ]);

        $acompte = new Acompte();
        $acompte->client_id = $validatedData['client_id'];
        $acompte->montant = $validatedData['montant'];
        $acompte->product_id = $validatedData['product_id'];
        $acompte->date = now();
        $acompte->status = 'en attente';
        $acompte->save();

        // Créer une nouvelle vente
        $sale = new Sale();
        $sale->client_id = $validatedData['client_id'];
        $sale->total_amount = $validatedData['montant'];
        $sale->montant_total_htva = round($sale->total_amount / (1 + (21/100)), 2);
        $sale->amount_tva = $sale->total_amount - $sale->montant_total_htva;
 // ou tout autre montant approprié
        $sale->datetime = now();
        // Ajoutez d'autres champs nécessaires pour la vente ici
        $sale->save();

        return response()->json(['message' => 'Acompte et vente créés avec succès', 'acompte_id' => $acompte->id, 'sale_id' => $sale->id], 201);
    }


public function apply(Request $request, $accountId)
{

    DB::transaction(function () use ($accountId, $request) {
        $acompte = Acompte::findOrFail($accountId);
        $acompte->status = 'appliqué';
        $acompte->save();
    });

    return response()->json(['message' => 'Acompte appliqué avec succès à la vente'], 200);
}

    public function refund(Request $request, $acompteId)
    {
        $acompte = Acompte::findOrFail($acompteId);

        if ($acompte->status !== 'en_attente') {
            return response()->json(['message' => 'L\'acompte ne peut pas être remboursé car il a déjà été appliqué ou remboursé.'], 400);
        }

        $acompte->status = 'remboursé';
        $acompte->save();

        return response()->json(['message' => 'Acompte remboursé avec succès', 'acompte_id' => $acompte->id], 200);
    }

}
