<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollection;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ShowController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // Récupérer uniquement les produits avec active = 1
        $products = Product::where('active', 1)->get();
        
        // Retourner la collection de produits sous forme de ressource
        return new ProductCollection($products);
    }
}
