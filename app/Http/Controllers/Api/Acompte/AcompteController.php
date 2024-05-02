<?php

namespace App\Http\Controllers\Api\Acompte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Acompte;
use App\Models\Sale;
use App\Models\Client;

class AcompteController extends Controller
{
	public function index()
	{
		$acomptes = Acompte::where('status', 'en_attente')->get();
		return response()->json($acomptes);
	}

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'montant' => 'required|numeric|min:0',
        ]);

        $acompte = new Acompte();
        $acompte->client_id = $validatedData['client_id'];
        $acompte->montant = $validatedData['montant'];
        $acompte->date = now();
        $acompte->status = 'en_attente';
        $acompte->save();

        return response()->json(['message' => 'Acompte créé avec succès', 'acompte_id' => $acompte->id], 201);
    }

    public function apply(Request $request, $saleId)
    {
        $sale = Sale::findOrFail($saleId);
        $acompte = Acompte::where('client_id', $sale->client_id)
                        ->where('status', 'en_attente')
                        ->firstOrFail();

        $sale->total_amount -= $acompte->montant;
        $sale->save();

        $acompte->status = 'appliqué';
        $acompte->save();

        return response()->json(['message' => 'Acompte appliqué avec succès à la vente', 'sale_id' => $sale->id], 200);
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
