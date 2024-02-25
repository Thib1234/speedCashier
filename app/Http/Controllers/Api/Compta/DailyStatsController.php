<?php

namespace App\Http\Controllers\Api\Compta;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use App\Models\Sale;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DailyStatsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    { 
        // Récupérer la date d'aujourd'hui
        $today = now()->format('Y-m-d');

        // Nombre total de ventes pour aujourd'hui
        $totalSales = Sale::whereDate('created_at', $today)->count();

        // Nombre total de clients pour aujourd'hui
        $totalClients = Client::whereHas('sales', function ($query) use ($today) {
            $query->whereDate('created_at', $today);
        })->count();

        // Montant total des paiements pour aujourd'hui
        $totalPayments = Payment::whereHas('sale', function ($query) use ($today) {
            $query->whereDate('created_at', $today);
        })->sum('total_amount');

        return response()->json([
            'total_sales' => $totalSales,
            'total_clients' => $totalClients,
            'total_payments' => $totalPayments,
        ]);
    }
}
