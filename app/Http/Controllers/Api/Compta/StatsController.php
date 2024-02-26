<?php

namespace App\Http\Controllers\Api\Compta;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Client;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StatsController extends Controller
{
    /**
     * Handle the incoming request.
     */

		
      public function __invoke(Request $request)
{
    

		// // Récupérer les dates de début et de fin à partir des paramètres de la requête
		// $startDate = $request->input('start');
		// $endDate = $request->input('end');
	
		// // Vérifier si les dates sont présentes et non vides
		// if (empty($startDate) || empty($endDate)) {
		// 	// Retourner une réponse avec un message d'erreur si les dates ne sont pas fournies
		// 	return response()->json(['error' => 'Veuillez fournir à la fois une date de début et une date de fin.'], 400);
		// }
	
		// // Vérifier si les dates sont au format valide
		// if (!strtotime($startDate) || !strtotime($endDate)) {
		// 	// Retourner une réponse avec un message d'erreur si les dates ne sont pas au format valide
		// 	return response()->json(['error' => 'Les dates fournies ne sont pas au format valide.'], 400);
		// }
	
		// Si les dates sont valides, continuer avec le traitement des statistiques
		// ...
	

        $startDate = $request->input('start');
        $endDate = $request->input('end');
        // Nombre total de ventes pour la période spécifiée
        $totalSales = Sale::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->count();

        // Nombre total de clients pour la période spécifiée
        $totalClients = Client::whereHas('sales', function ($query) use ($startDate, $endDate) {
            $query->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate);
        })->count();

        // Montant total des paiements pour la période spécifiée
        $totalPayments = Payment::whereHas('sale', function ($query) use ($startDate, $endDate) {
            $query->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate);
        })->sum('total_amount');

        return response()->json([
            'total_sales' => $totalSales,
            'total_clients' => $totalClients,
            'total_payments' => $totalPayments,
        ]);
      }
}
