<?php

namespace App\Http\Controllers\Api\Compta;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Client;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\Product;

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
        $totalSales = Sale::whereDate('created_at', $today)->count();
        
        $totalAmount = Sale::whereDate('created_at', $today)->sum('total_amount');


        // Nombre total de clients pour aujourd'hui
        $totalClients = Client::whereHas('sales', function ($query) use ($today) {
            $query->whereDate('created_at', $today);
        })->count();

        // Montant total des paiements pour aujourd'hui
        // $totalPayments = Payment::whereHas('sale', function ($query) use ($today) {
        //     $query->whereDate('created_at', $today);
        // })->sum('total_amount');


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
        ]);
    }
}