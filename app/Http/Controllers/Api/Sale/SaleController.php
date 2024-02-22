<?php

namespace App\Http\Controllers\Api\Sale;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Client;
use App\Models\Payment;
use App\Models\ProductSale;


class SaleController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0', // Ajoutez une validation pour le prix
            'client_id' => 'nullable|exists:clients,id',
            'total_amount' => 'required|numeric|min:0',
        ]);
    
        // Créer une nouvelle vente
        $sale = new Sale();
        $sale->datetime = now();
        $sale->client_id = $request->input('client_id');
        $sale->save();
    
        // Enregistrer les produits vendus
        foreach ($request->input('products') as $product) {
            $productSale = new ProductSale(); // Créez une instance de ProductSale
            $prod = Product::find($product['id']);
            $prod->stock = $prod->stock - $product['quantity'];
            
            $productSale->sale_id = $sale->id;
            $productSale->product_id = $product['id'];
            $productSale->quantity = $product['quantity'];
            
            $productSale->price = $product['price']; // Ajoutez le prix du produit
            $productSale->save();
            $prod->save();
        }
    
        // Enregistrer le paiement de la vente
        $payment = new Payment();
        $payment->sale_id = $sale->id;
        // $payment->method = $request->input('payment_method');
        $payment->total_amount = $request->input('total_amount');
        $payment->cash = $request->input('cash');
        $payment->bancontact = $request->input('bancontact');
        $payment->credit_card = $request->input('credit_card');
        $payment->save();
    
        // Réponse de succès
        return response()->json(['message' => 'Vente enregistrée avec succès'], 201);
    }
}