<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductMinimumCollection;
use App\Http\Resources\CategoryCollection;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ShowController extends Controller
{
    public function __invoke(Request $request)
    {
        $products = Product::select('id', 'name', 'price', 'category_id')->where('active', 1)->get();
        $categories = Category::all();

        return response()->json([
            'products' => new ProductMinimumCollection($products),
            'categories' => new CategoryCollection($categories),
        ]);
    }
}
