<?php

namespace App\Http\Controllers\Api\Facture;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Facture;
use Illuminate\Http\JsonResponse;

class ListController extends Controller
{
    public function __invoke(Request $request)
    {
        $factures = Facture::with('client', 'sale')->where('send', 0)->get();
        return response()->json([
            'factures' => $factures
        ]);
    }
}
