<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Http\Resources\ProductCollection;

class ProductExcludingToilettageController extends Controller
{
    public function __invoke(Request $request)
    {
        // Récupérer tous les produits sauf ceux de la catégorie toilettage
        $excludedCategoryName = 'Toilettage'; // Le nom de la catégorie à exclure
        $products = Product::where('active', 1)
            ->where('stock', '>', '1')
            ->whereDoesntHave('category', function ($query) use ($excludedCategoryName) {
                $query->where('name', $excludedCategoryName);
            })->get();

        return new ProductCollection($products);
    }
}
