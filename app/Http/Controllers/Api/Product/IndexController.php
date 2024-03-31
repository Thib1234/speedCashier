<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\CategoryCollection;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class IndexController extends Controller
{

    public function __invoke(Request $request)
    {
        $products = Product::all();
        $categories = Category::all();

        return response()->json([
            'products' => new ProductCollection($products),
            'categories' => new CategoryCollection($categories),
        ]);
    }
}
