<?php

namespace App\Http\Controllers\Api\StatToilettage;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\Product;
use DatePeriod;
use DateInterval;
use DateTime;


class DailyStatController extends Controller
{
    public function __invoke(Request $request)
    { 
        // Récupérer la date d'aujourd'hui
        $today = now()->format('Y-m-d');
        
        // Récupérer les ventes avec au moins un produit de la catégorie "Toilettage"
        $sales = Sale::whereDate('created_at', $today)
            ->with(['products' => function ($query) {
                $query->whereHas('category', function ($query) {
                    $query->where('name', 'Toilettage');
                });
            }, 'client'])
            ->get();

        // Filtrer les ventes pour inclure uniquement celles qui n'ont que des produits de la catégorie "Toilettage"
        $filteredSales = $sales->map(function ($sale) {
            $sale->products = $sale->products->filter(function ($product) {
                return $product->category->name === 'Toilettage';
            });
            return $sale;
        })->filter(function ($sale) {
            return $sale->products->isNotEmpty();
        });

        // Nombre total de ventes du jour
        $totalSales = $filteredSales->count();
        
        $totalAmount = $filteredSales->sum('total_amount');

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
        foreach ($filteredSales as $sale) {
            foreach ($sale->products as $product) {
                $saleLine = [
                    'id' => $sale->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $product->pivot->quantity,
                    'price' => $product->pivot->price,
                    // Ajoutez d'autres champs pertinents au besoin
                ];
                $saleLines[] = $saleLine;
            }
        }

        return response()->json([
            'total_sales' => $totalSales,
            'total_clients' => $totalClients,
            'sale_lines' => $saleLines,
            'sales' => $filteredSales,
            'totalAmount' => $totalAmount,
            'salesByHour' => $salesByHour,
            'labels' => $labels,
        ]);
    }
}