<?php

namespace App\Http\Controllers\Api\Pdf;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use PDF;

class TicketController extends Controller
{
    /**
     * Génère et renvoie un ticket PDF pour la vente spécifiée.
     *
     * @param int $saleId L'ID de la vente
     * @return \Illuminate\Http\Response
     */
    public function __invoke($saleId)
    {
        // Récupérer la vente et ses relations (produits, client, paiements)
        $sale = Sale::with(['products', 'client', 'payments'])->findOrFail($saleId);

        // Générer le contenu HTML du ticket PDF
        $htmlContent = view('pdf.ticket', compact('sale'))->render();

        // Utiliser la classe PDF pour générer le PDF
        $pdf = PDF::loadHTML($htmlContent);

        // Définir la taille et l'orientation du papier
        $pdf->setPaper('A4', 'portrait');

        // Renvoyer la réponse avec le PDF en tant que contenu
        return $pdf->stream();
    }
}
