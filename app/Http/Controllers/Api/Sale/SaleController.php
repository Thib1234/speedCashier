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
        'products.*.id' => 'nullable|exists:products,id', // Permet également les produits temporaires
        // 'products.*.name' => 'required|string', // Ajoutez une validation pour le nom du produit temporaire
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
        if(isset($product['id'])) { // Vérifiez si le produit a un ID
            // Si le produit a un ID, cela signifie qu'il n'est pas temporaire
            $productSale = new ProductSale(); // Créez une instance de ProductSale
            $prod = Product::find($product['id']);
            $prod->stock = $prod->stock - $product['quantity'];

            $productSale->sale_id = $sale->id;
            $productSale->product_id = $product['id'];
            $productSale->quantity = $product['quantity'];

            $productSale->price = $product['price']; // Ajoutez le prix du produit
            $productSale->save();
            $prod->save();
        } else {
            // Si le produit n'a pas d'ID, cela signifie qu'il est temporaire
            // Gérez le produit temporaire selon vos besoins, par exemple, vous pouvez le logger ou l'ignorer
        }
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
    return response()->json(['message' => 'Vente enregistrée avec succès', 'sale_id' => $sale->id], 201);
}

}