<?php

namespace App\Http\Controllers\Api\Categorie;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryCollection;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class IndexController extends Controller
{

    public function __invoke(Request $request)
    {
        return new CategoryCollection(
            resource: Category::all(),
        );
    }
}
