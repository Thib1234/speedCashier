<?php

namespace App\Http\Controllers\Api\Facture;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Client;
use Illuminate\Http\JsonResponse;

class CreateController extends Controller
{
     public function __invoke(Request $request)
    {
        $sales = Sale::with(['products', 'client'])->paginate(20);
        $clients = Client::select('id', 'name')->get();
        return response()->json([
            'sales' => $sales,
            'clients' => $clients
        ]);
    }
}
