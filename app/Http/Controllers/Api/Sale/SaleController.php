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
            'products.*.id' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (substr($value, 0, 1) !== '_' && !Product::where('id', $value)->exists()) {
                        $fail('L\'ID du produit sélectionné n\'est pas valide.');
                    }
                }
            ],
            'products.*.quantity' => 'integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
            'client_id' => 'nullable|exists:clients,id',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $sale = new Sale();
        $sale->datetime = now();
        $sale->client_id = $request->input('client_id');
        $sale->save();

        foreach ($request->input('products') as $product) {
            if (isset($product['id'])) {
                if (substr($product['id'], 0, 1) !== '_') {
                    $productSale = new ProductSale();
                    $prod = Product::find($product['id']);
                    if ($prod !== null) {
                        $prod->stock = $prod->stock - $product['quantity'];
                        $productSale->sale_id = $sale->id;
                        $productSale->product_id = $product['id'];
                        $productSale->quantity = $product['quantity'];
                        $productSale->price = $product['price'];
                        $productSale->save();
                        $prod->save();
                    } else {
                        // Gérer le cas où le produit n'existe pas dans la base de données
                        // Par exemple, journaliser cette situation ou traiter différemment
                    }
                } else {
                    // Gérer le cas où le produit est temporaire
                    // Par exemple, le journaliser ou l'ignorer
                }
            } else {
                // Gérer le cas où l'ID du produit est manquant
                // Par exemple, journaliser cette situation ou traiter différemment
            }
        }

        $payment = new Payment();
        $payment->sale_id = $sale->id;
        $payment->total_amount = $request->input('total_amount');
        $payment->cash = $request->input('cash');
        $payment->bancontact = $request->input('bancontact');
        $payment->credit_card = $request->input('credit_card');
        $payment->save();

        return response()->json(['message' => 'Vente enregistrée avec succès', 'sale_id' => $sale->id], 201);
    }
}
