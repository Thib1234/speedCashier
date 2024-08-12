<?php

namespace App\Http\Controllers\Api\Compta;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Client;
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

        $sales = Sale::whereDate('created_at', $today)->with('products', 'client')->get();

        $totalSales = $sales->count();
        
        $totalAmount = $sales->sum('total_amount');

        $totalClients = Client::whereHas('sales', function ($query) use ($today) {
            $query->whereDate('created_at', $today);
        })->count();

        $startDate = (new DateTime('today 06:00:00'))->format('Y-m-d H:00:00');
        $endDate = (new DateTime('today 20:00:00'))->format('Y-m-d H:00:00');
        
        $salesByHour = [];

        foreach ($sales as $sale) {
            $saleDate = new DateTime($sale->created_at);
            if ($saleDate >= new DateTime($startDate) && $saleDate <= new DateTime($endDate)) {
                $hour = $saleDate->format('H');
                if (!isset($salesByHour[$hour])) {
                    $salesByHour[$hour] = 0;
                }
                $salesByHour[$hour] += $sale->total_amount;
            }
        }

        ksort($salesByHour);

        $labels = array_keys($salesByHour);
        $period = new DatePeriod(
            new DateTime($startDate),
            new DateInterval('PT1H'), // Interval de 1 heure
            (new DateTime($endDate))->modify('+1 hour')
        );

        $saleLines = [];
        foreach ($sales as $sale) {
            $product = Product::find($sale->product_id);
            if ($product) {
                $saleLine = [
                    'id' => $sale->id,
                    'product_id' => $sale->product_id,
                    'product_name' => $product->name,
                    'quantity' => $sale->quantity,
                    'price' => $sale->price,
                ];
                $saleLines[] = $saleLine;
            } else {
                $saleLine = [
                    'id' => $sale->id,
                    'product_id' => $sale->product_id,
                    'product_name' => 'Produit inconnu',
                    'quantity' => $sale->quantity,
                    'price' => $sale->price,
                ];
                $saleLines[] = $saleLine;
            }
        }
            return response()->json([
                'total_sales' => $totalSales,
                'total_clients' => $totalClients,
                'sale_lines' => $saleLines,
                'sales' => $sales,
                'totalAmount' => $totalAmount,
                'salesByHour' => $salesByHour,
                'labels' => $labels,
            ]);
        }
}