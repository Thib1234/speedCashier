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
    const CATEGORY_NAME = 'Toilettage';
    const START_TIME = '00:00:01';
    const END_TIME = '23:59:59';

    public function __invoke(Request $request)
    { 
        $today = now()->format('Y-m-d');
        $sales = $this->getSales($today);
        $filteredSales = $this->filterSales($sales);
        $totalSales = $filteredSales->count();
        $totalAmount = $filteredSales->sum(function ($sale) {
            return $sale->products->sum('price');
        });
        $totalClients = $this->getTotalClients($today);
        $salesByHour = $this->getSalesByHour($filteredSales);
        $saleLines = $this->getSaleLines($filteredSales);

        return response()->json([
            'total_sales' => $totalSales,
            'total_clients' => $totalClients,
            'sale_lines' => $saleLines,
            'sales' => $filteredSales,
            'totalAmount' => $totalAmount,
            'salesByHour' => $salesByHour,
            'labels' => array_keys($salesByHour),
        ]);
    }

    private function getSales($date)
    {
        return Sale::whereDate('created_at', $date)
            ->with(['client', 'products.category'])
            ->get();
    }

    private function filterSales($sales)
    {
        return $sales->map(function ($sale) {
            $filteredProducts = $sale->products->filter(function ($product) {
                return $product->category && $product->category->name === self::CATEGORY_NAME;
            });
        
            if ($filteredProducts->isEmpty()) {
                return null;
            }
        
            $filteredSale = clone $sale;
            $filteredSale->setRelation('products', $filteredProducts);
            $filteredSale->total_amount = $filteredProducts->sum('price');
        
            return $filteredSale;
        })->filter();
    }

    private function getTotalClients($date)
    {
        return Client::whereHas('sales', function ($query) use ($date) {
            $query->whereDate('created_at', $date);
        })->count();
    }

    private function getSalesByHour($sales)
    {
        $startDate = (new DateTime('today ' . self::START_TIME))->format('Y-m-d H:00:00');
        $endDate = (new DateTime('today ' . self::END_TIME))->format('Y-m-d H:00:00');
        $salesByHour = [];

        foreach ($sales as $sale) {
            $saleDate = new DateTime($sale->created_at);
            if ($saleDate >= new DateTime($startDate) && $saleDate <= new DateTime($endDate)) {
                $hour = $saleDate->format('H'); // Obtenir uniquement l'heure
                if (!isset($salesByHour[$hour])) {
                    $salesByHour[$hour] = 0;
                }
                $salesByHour[$hour] += $sale->total_amount;
            }
            ksort($salesByHour);
            return $salesByHour;
        }
    }

    private function getSaleLines($sales)
    {
        return $sales->flatMap(function ($sale) {
            return $sale->products->map(function ($product) use ($sale) {
                return [
                    'id' => $sale->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $product->pivot->quantity,
                    'price' => $product->pivot->price,
                ];
            });
        })->values();
    }
}
    
