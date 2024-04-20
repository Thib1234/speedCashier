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

class ShowStatsController extends Controller
{
    public function stats(Request $request)
    {

        $startDate = $request->input('start');
        $endDate = $request->input('end');
        $salesByDay = [];

        // $sales = Sale::whereDate('created_at', '>=',$startDate)->with('products', 'client')
        //     ->whereDate('created_at', '<=', $endDate)
        //     ->get();
        // // Nombre total de ventes du jour

		 // Récupération des ventes par jour V2 a tester
		// $sales = Sale::whereDate('created_at', '>=',$startDate)
		// ->whereDate('created_at', '<=', $endDate)
		// ->with('products', 'client')
		// ->get();

		$sales = Sale::whereDate('created_at', '>=', $startDate)->with(['products' => function ($query){
			$query->whereHas('category', function ($query) {
				$query->where('name', 'Toilettage');
			});
		}, 'client'])
		->whereDate('create_at', '<=', $endDate)
		->get();

		$filteredSales = $sales->map(function ($sale) {
            $sale->products = $sale->products->filter(function ($product) {
                return $product->category->name === 'Toilettage';
            });
            return $sale;
        })->filter(function ($sale) {
            return $sale->products->isNotEmpty();
        });
        
        $totalSales = $filteredSales->sum('total_amount');

        // Nombre total de clients pour aujourd'hui
        $totalClients = Client::whereHas('sales', function ($query) use ($startDate, $endDate) {
            $query->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate);
        })->count();

        // Calcul du total des ventes par jour
        foreach ($sales as $sale) {
            $date = $sale->created_at->toDateString();
            if (!isset($salesByDay[$date])) {
                $salesByDay[$date] = 0;
            }
            $salesByDay[$date] += $sale->total_amount;
        }

        // Remplir les jours sans ventes avec un total de 0
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

        // Trier le tableau par date
        ksort($salesByDay);

        $saleLines = [];
        foreach ($filteredSales as $sale) {
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
            'sale_lines' => $saleLines,
            'sales' => $filteredSales,
            'salesByDay' => $salesByDay,
            // 'dailyTotal' => $dailyTotals,
        ]);
    }
	public function show(Request $request)
    {
        $startDate = $request->query('start');
        $endDate = $request->query('end');
        $salesByDay = [];

        $sales = Sale::whereDate('created_at', '>=',$startDate)->with('products', 'client')
            ->whereDate('created_at', '<=', $endDate)
            ->get();
            // Nombre total de ventes du jour
            
        $totalSales = Sale::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->sum('total_amount');

        $totalSalesHtva = Sale::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->sum('montant_total_htva');   
                
        $totalHtva = $totalSales - $totalSalesHtva;

            // Nombre total de clients pour aujourd'hui
        $totalClients = Client::whereHas('sales', function ($query) use ($startDate, $endDate) {
            $query->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate);
        })->count();

        

        // Récupération des ventes par jour
        $sales = Sale::whereDate('created_at', '>=',$startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->with('products', 'client')
            ->get();

        // Calcul du total des ventes par jour
        foreach ($sales as $sale) {
            $date = $sale->created_at->toDateString();
            if (!isset($salesByDay[$date])) {
                $salesByDay[$date] = 0;
            }
            $salesByDay[$date] += $sale->total_amount;
        }

        // Remplir les jours sans ventes avec un total de 0
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

        // Trier le tableau par date
        ksort($salesByDay);

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
		return view('compta.show', [
			'total_sales' => $totalSales,
			'total_clients' => $totalClients,
			'sale_lines' => $saleLines,
			'sales' => $sales,
			'salesByDay' => $salesByDay,
			'startDate' => $startDate, // Ajoutez les valeurs de startDate et endDate à la vue
			'endDate' => $endDate,
            'totalSalesHtva' => $totalSalesHtva,
            'totalHtva' => $totalHtva,
		]);
	}

}
