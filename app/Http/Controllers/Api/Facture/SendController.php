<?php

namespace App\Http\Controllers\Api\Facture;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Facture;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;


class SendController extends Controller
{
	public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => 'required|integer',
        ]);

        $facture = Facture::with('client', 'sale', 'sale.products')->findOrFail($validated['id']);

        // return response()->json([
        //     'facture' => $facture,
        // ]);

        $pdf = Pdf::loadview('factures.pdf.view', ['facture' => json_decode($facture, true)]);
        $pdfData = [
            'data' => $pdf->output(),
            'name' => "facture-{$facture['id']}.pdf",
        ];

        $client = $facture->client;

        Mail::send('emails.facture', ['client' => $client], function($message) use ($client, $pdfData) {
            $message->to($client->email)
                    ->subject("Votre Facture")
                    ->attachData($pdfData['data'], $pdfData['name']);
        });

        $facture->send = true;
        $facture->save();
        return response()->json([
            'success' => true,
            'factureId' => $facture,
        ]);
    }
    
}