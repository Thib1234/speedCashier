<?php

namespace App\Http\Controllers\Api\Compta;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DatePeriod;
use DateInterval;
use DateTime;

class StatsController extends Controller
{
    public function __invoke(Request $request)
    {
        $startDate = $request->input('start');
        $endDate = $request->input('end');

        $sales = Sale::whereDate('created_at', '>=',$startDate)->with('products', 'client', 'payment')
            ->whereDate('created_at', '<=', $endDate)
            ->get();
        // Nombre total de ventes du jour
        
        $totalSales = Sale::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->count();

        // Nombre total de clients pour aujourd'hui
        $totalClients = Client::whereHas('sales', function ($query) use ($startDate, $endDate) {
            $query->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate);
        })->count();

        // Montant total des paiements pour aujourd'hui
        $totalPayments = Payment::whereHas('sale', function ($query) use ($startDate, $endDate) {
            $query->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate);
        })->sum('total_amount');


        //// BLOQUE DE CODE POUR RECUPERER CHAQUE SOMME TOTALES DES VENTES PAR JOUR
        $period = new DatePeriod(
            new DateTime($startDate),
            new DateInterval('P1D'),
            (new DateTime($endDate))->modify('+1 day')
        );

        $dailyTotals = [];
        foreach ($period as $date) {
            $dateKey = $date->format('Y-m-d');

            $totalAmount = Payment::whereHas('sale', function ($query) use ($dateKey) {
                $query->whereDate('created_at', '=', $dateKey);
            })->sum('total_amount');
            $dailyTotals[] = ['date' => $dateKey,
            'total_amount' => $totalAmount];
        }
        ////////// FIN DU BLOC DE CODE

    $saleLines = [];
    foreach ($sales as $sale) {
        $product = Product::find($sale->product_id);
    
        // Vérifiez si le produit existe avant d'essayer d'accéder à ses propriétés
        if ($product) {
            $saleLine = [
                'id' => $sale->id,
                'product_id' => $sale->product_id,
                'product_name' => $product->name, // Ajout du nom du produit
                'quantity' => $sale->quantity,
                'price' => $sale->price,
                // Ajoutez d'autres champs pertinents au besoin
            ];
            $saleLines[] = $saleLine;
        } else {
            // Gérer le cas où le produit n'existe pas
            // Vous pouvez par exemple assigner un nom générique ou laisser vide
            $saleLine = [
                'id' => $sale->id,
                'product_id' => $sale->product_id,
                'product_name' => 'Produit inconnu',
                'quantity' => $sale->quantity,
                'price' => $sale->price,
                // Ajoutez d'autres champs pertinents au besoin
            ];
            $saleLines[] = $saleLine;
        }
    }
        return response()->json([
            'total_sales' => $totalSales,
            'total_clients' => $totalClients,
            'total_payments' => $totalPayments,
            'sale_lines' => $saleLines,
            'sales' => $sales,
            'dailyTotal' => $dailyTotals,
        ]);
    }
}
