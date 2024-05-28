<?php

namespace App\Http\Controllers\Api\Facture;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;

class CreateController extends Controller
{
    /**
     * Handle the incoming request.
     */

	public function __invoke(Request $request)
	{
		$sales = Sale::with('client')->get();
		return response()->json($sales);
	}
}
