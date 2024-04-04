<?php

namespace App\Http\Controllers\Api\Compta;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Client;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\Product;
use DatePeriod;
use DateInterval;
use DateTime;


class DailyStatsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    { 
        // Récupérer la date d'aujourd'hui
        $today = now()->format('Y-m-d');
        // $sales = Sale::whereDate('created_at', $today)->get();

        $sales = Sale::whereDate('created_at', $today)->with('products', 'client')->get();
         //dd($sale);
        // Nombre total de ventes du jour
        $totalSales = $sales->count();
        
        $totalAmount = $sales->sum('total_amount');


        // Nombre total de clients pour aujourd'hui
        $totalClients = Client::whereHas('sales', function ($query) use ($today) {
            $query->whereDate('created_at', $today);
        })->count();

        $startDate = (new DateTime('today 06:00:00'))->format('Y-m-d H:00:00');
        ; // Début de la période à 7h00 aujourd'hui
        $endDate = (new DateTime('today 20:00:00'))->format('Y-m-d H:00:00'); // Fin de la période à 20h00 aujourd'hui
        
        $salesByHour = []; // Initialise un tableau pour stocker les ventes par heure

        foreach ($sales as $sale) {
            $saleDate = new DateTime($sale->created_at);
            if ($saleDate >= new DateTime($startDate) && $saleDate <= new DateTime($endDate)) {
                $hour = $saleDate->format('H'); // Obtenir uniquement l'heure
                if (!isset($salesByHour[$hour])) {
                    $salesByHour[$hour] = 0;
                }
                $salesByHour[$hour] += $sale->total_amount;
            }
        }

        // Trier le tableau par heure
        ksort($salesByHour);

        $labels = array_keys($salesByHour);

        
        // Remplir les heures sans ventes avec un total de 0
        $period = new DatePeriod(
            new DateTime($startDate),
            new DateInterval('PT1H'), // Interval de 1 heure
            (new DateTime($endDate))->modify('+1 hour')
        );

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
                // 'total_payments' => $totalPayments,
                'sale_lines' => $saleLines,
                'sales' => $sales,
                'totalAmount' => $totalAmount,
                'salesByHour' => $salesByHour,
                'labels' => $labels,
            ]);
        }
}