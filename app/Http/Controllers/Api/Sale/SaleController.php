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
        $sale->total_amount = $request->input('total_amount');
        $sale->cash = $request->input('cash');
        $sale->bancontact = $request->input('bancontact');
        $sale->credit_card = $request->input('credit_card');
        $sale->virement = $request->input('virement');
        $sale->stripe = $request->input('stripe');
        $sale->montant_total_htva = round($request->input('total_amount') / (1 + (21/100)), 2);
        $sale->amount_tva = $request->input('total_amount') - $sale->montant_total_htva;
        $sale->save();

        foreach ($request->input('products') as $product) {
            if (substr($product['id'], 0, 1) === '_') {
                $tempProduct = new Product();
                $tempProduct->name = $product['name'];
                $tempProduct->price = $product['price'];
                $tempProduct->save();

                $productSale = new ProductSale();
                $productSale->sale_id = $sale->id;
                $productSale->product_id = $tempProduct->id;
                $productSale->quantity = $product['quantity'];
                $productSale->price = $product['price'];
                $productSale->total = $product['price'] * $product['quantity'];
                $productSale->total_htva = round($productSale->total / (1 + (21/100)), 2);
                $productSale->save();
            } else { // Si le produit n'est pas temporaire
                $prod = Product::find($product['id']);
                $prod->stock = $prod->stock - $product['quantity'];

                // Vérifier si le produit existe déjà dans la vente
                $existingProductSale = ProductSale::where('sale_id', $sale->id)
                    ->where('product_id', $product['id'])
                    ->first();

                if ($existingProductSale) {
                    // Mettre à jour la quantité et le total si le produit existe déjà
                    $existingProductSale->quantity += $product['quantity'];
                    $existingProductSale->total += $product['price'] * $product['quantity'];
                    $existingProductSale->total_htva = round($existingProductSale->total / (1 + (21/100)), 2);
                    $existingProductSale->save();
                } else {
                    // Ajouter le produit à la vente s'il n'existe pas déjà
                    $productSale = new ProductSale();
                    $productSale->sale_id = $sale->id;
                    $productSale->product_id = $product['id'];
                    $productSale->quantity = $product['quantity'];
                    $productSale->price = $product['price'];
                    $productSale->total = $product['price'] * $product['quantity'];
                    $productSale->total_htva = round($productSale->total / (1 + (21/100)), 2);
                    $productSale->save();
                }

                $prod->save();
            }
        }
        return response()->json(['message' => 'Vente enregistrée avec succès', 'sale_id' => $sale->id], 201);
    }
}
