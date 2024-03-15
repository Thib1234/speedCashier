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
            // Vérifier si le produit est temporaire
            if (substr($product['id'], 0, 1) === '_') {
                //Créer le produit temporaire s'il n'existe pas déjà
                $tempProduct = new Product();
                $tempProduct->name = $product['name']; // Assurez-vous d'avoir le nom du produit dans la demande
                $tempProduct->price = $product['price'];
                // Autres attributs du produit temporaire peuvent être définis ici
                $tempProduct->save();
                
                // Ajouter le produit temporaire à la vente
                $productSale = new ProductSale();
                $productSale->sale_id = $sale->id;
                $productSale->product_id = $tempProduct->id;
                $productSale->quantity = $product['quantity'];
                $productSale->price = $product['price'];
                $productSale->save();
            } else {
                // Si le produit n'est pas temporaire, ajoutez-le directement à la vente
                $productSale = new ProductSale();
                $productSale->sale_id = $sale->id;
                $productSale->product_id = $product['id'];
                $productSale->quantity = $product['quantity'];
                $productSale->price = $product['price'];
                $productSale->save();
            }
        }
    
        // Créer un nouvel enregistrement Payment pour la vente
        $payment = new Payment();
        $payment->sale_id = $sale->id;
        $payment->total_amount = $request->input('total_amount');
        $payment->cash = $request->input('cash');
        $payment->bancontact = $request->input('bancontact');
        $payment->credit_card = $request->input('credit_card');
        $payment->virement = $request->input('virement');
        $payment->save();

        // Associer le paiement à la vente
        $sale->payment_id = $payment->id;
        $sale->save();

        return response()->json(['message' => 'Vente enregistrée avec succès', 'sale_id' => $sale->id], 201);
    }
}
