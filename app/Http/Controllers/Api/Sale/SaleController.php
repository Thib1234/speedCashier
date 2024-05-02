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
                $productSale->save();
            } else { // Si le produit n'est pas temporaire
                $prod = Product::find($product['id']);
                $prod->stock = $prod->stock - $product['quantity'];
                $productSale = new ProductSale();
                $productSale->sale_id = $sale->id;
                $productSale->product_id = $product['id'];
                $productSale->quantity = $product['quantity'];
                $productSale->price = $product['price'];
                $productSale->total = $product['price'] * $product['quantity'];
                $productSale->save();
                $prod->save();
            }
        }
        return response()->json(['message' => 'Vente enregistrée avec succès', 'sale_id' => $sale->id], 201);
    }
}
