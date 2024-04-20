<?php

namespace App\Http\Controllers\Api\StatToilettage;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Client;
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
        $salesByDay = [];

		$sales = Sale::whereDate('created_at', '>=', $startDate)->with(['products' => function ($query){
			$query->whereHas('category', function ($query) {
				$query->where('name', 'Toilettage');
			});
		}, 'client'])
		->whereDate('created_at', '<=', $endDate)
		->get();

		$filteredSales = $sales->map(function ($sale) {
            $sale->products = $sale->products->filter(function ($product) {
                return $product->category->name === 'Toilettage';
            });
            return $sale;
        })->filter(function ($sale) {
            return $sale->products->isNotEmpty();
        })->load('products'); // Charger tous les produits en une seule fois
        
        $totalSales = $filteredSales->sum('total_amount');
        
        $totalClients = Client::whereHas('sales', function ($query) use ($startDate, $endDate) {
            $query->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate);
        })->count();

        foreach ($filteredSales as $sale) {
            $date = $sale->created_at->toDateString();
            if (!isset($salesByDay[$date])) {
                $salesByDay[$date] = 0;
            }
            $salesByDay[$date] += $sale->total_amount;
        }

        $period = new DatePeriod(
            new DateTime($startDate),
            new DateInterval('P1D'),
            (new DateTime($endDate))->modify('+1 day')
        );

        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            if (!isset($salesByDay[$dateString])) {
                $salesByDay[$dateString] = 0;
            }
        }

        ksort($salesByDay);

        $saleLines = [];

        foreach ($filteredSales as $sale) {
            $product = $sale->product; // Utiliser le produit chargé
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
            'sale_lines' => $saleLines,
            'sales' => $filteredSales,
            'salesByDay' => $salesByDay,
            // 'dailyTotal' => $dailyTotals,
        ]);
    }
}
