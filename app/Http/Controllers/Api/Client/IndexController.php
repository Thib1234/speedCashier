<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientCollection;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class IndexController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return new ClientCollection(
            resource: Client::all(),
        );
    }
}
