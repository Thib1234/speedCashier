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
        $sales = Sale::whereDate('created_at', '>=', $startDate)->with(['client', 'products.category'])
            ->whereDate('created_at', '<=', $endDate)
            ->get();
            $filteredSales = $sales->map(function ($sale) {
                $filteredProducts = $sale->products->filter(function ($product) {
                    return $product->category && $product->category->name === 'Toilettage';
                });
                if ($filteredProducts->isEmpty()) {
                    return null;
                }
                $filteredSale = clone $sale;
                $filteredSale->setRelation('products', $filteredProducts);
                $filteredSale->total_amount = $filteredProducts->sum('price');
                return $filteredSale;
            })->filter();
        $totalSales = $filteredSales->count();
        $totalAmount = $filteredSales->sum(function ($sale) {
            return $sale->products->sum('price');
        });
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
            foreach ($sale->products as $product) {
                $saleLine = [
                    'id' => $sale->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $product->pivot->quantity,
                    'price' => $product->pivot->price,
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
            'chien' => $totalAmount
            // 'dailyTotal' => $dailyTotals,
        ]);
    }
}
