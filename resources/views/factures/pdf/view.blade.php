<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture #: {{ $facture['id'] }}</title>
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            /* Ajout de la hauteur à 100% */
            position: relative;
            /* Position relative pour permettre le positionnement du footer */
        }

        header {
            color: #5F5B94;
            text-align: center;
            padding: 10px 0;
            width: 100%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            margin: 0;
            font-size: 2em;
            font-weight: 10;
        }

        h2 {
            text-transform: capitalize;
            font-size: 14px;
            text-align: left;
            width: 90%;
            margin: auto;
            font-weight: 5;
        }

        .table {
            width: 90%;
            margin: auto;
            border-collapse: collapse;
        }

        .tableheader,
        .cell {
            border: 1px solid #9DC1AF;
            padding: 10px;
            text-align: center;
        }
        .infoprix{
            background-color: #E6EEEA;
            border-bottom: 3px solid #83B19B;
            text-align: right;
        }

        .tableheader {
            color: #5F5B94;
        }

        thead tr th {
            border: 1px solid white;
            border-bottom: 1px solid #9DC1AF;
        }

        hr {
            width: 90%;
            margin: auto;
            color: #9DC1AF;
        }

        section {
            width: 90%;
            margin: auto;
        }

        h3 {
            color: #5F5B94;
            margin-bottom: 0;
            font-size: 10px;
        }

        h4 {
            margin-bottom: 0;
            font-size: 10px;

        }

        .droite {
            text-align: right;
        }

        section h4 {
            margin-top: 0;
            font-weight: 5;
            font-size: 10px;
        }

        footer {
            width: 100%;
            padding: 20px;
            position: absolute;
            /* Position absolue pour ancrer le footer en bas */
            bottom: 0;
            /* Ancre le footer en bas */
        }

        /* Style pour les trois parties */
        .footer-table {
            width: 100%;
            table-layout: fixed;
        }

        .footer-table td {
            text-align: center;
            padding: 10px;
        }

        .footer-left {
            width: 33.33%;
        }

        .footer-center {
            width: 33.33%;
        }

        .footer-right {
            width: 33.33%;
        }

    </style>
</head>
<body>
    <header>
        <h1>
			Cesar Et Rosalie
        </h1>
        <h2>
            Rue des Combattants, 11
        </h2>
        <h2>
            1310 La Hulpe
        </h2>
        <h2>
            BE 01002.475.006
        </h2>
        <hr>
    </header>
    <table class="table" style="margin-bottom: 80px;">
        <tbody>
            <tr>
                <td>
                    <h3>DATE :</h3>
                    <h4>
                        {{$facture['date_facture']}}
                    </h4>
                    <h3>N° DE FACTURE :</h3>
                    <h4>Facture : {{ $facture['id'] }}</h4>
                </td>
                <td colspan="4"></td>
                <td>
                    <h3 class="droite">A :</h3>
                    <h4 class="droite">{{ $facture['client']['company'] }}</h4>
                    <h4 class="droite">{{ $facture['client']['adresse'] }}</h4>
                    <h4 class="droite">{{ $facture['client']['ville'] }}{{ $facture['client']['code_postal'] }}</h4>
                    <h4 class="droite">TVA : BE{{ $facture['client']['tva'] }}</h4>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="table">
        <!-- En-tête -->
        <thead>
            <tr class="tableheader">
                <th style="width: 5%;">QTE</th>
                <th style="width: 40%;">Produit</th>
                <th>Prix Unitaire HTVA</th>
                <th style="width: 25%;">Total HTVA</th>
                <th style="width: 15%;">Total TTC</th>
            </tr>
        </thead>
        <!-- Corps de la table -->
        <tbody>
            <!-- Ligne 1 -->
            @foreach($facture['sale']['products'] as $item)
            <tr>
                <td class="cell">{{ $item['pivot']['quantity'] }}</td>
                <td class="cell">{{ $item['name'] }}</td>
                <td class="cell">{{ $item['price_htva'] }} €</td>
                <td class="cell">{{ $item['pivot']['total_htva'] }} €</td>
                <td class="cell">{{ $item['pivot']['total'] }} €</td>
            </tr>
            @endforeach
            @for ($i = count($facture['sale']['products']); $i < 8; $i++) <tr>
                <td class="cell">&nbsp;</td>
                <td class="cell">&nbsp;</td>
                <td class="cell">&nbsp;</td>
                <td class="cell">&nbsp;</td>
                <td class="cell">&nbsp;</td>
                </tr>
            @endfor
        </tbody>
        <!-- Pied de page de la table -->
        <tfoot>
            <tr>
                <td colspan="3"></td>
                <td class="cell tableheader">SOUS-TOTAL :
                </td>
                <td class="cell infoprix" style="background-color: #E6EEEA;">{{ $facture['sale']['montant_total_htva'] }} €</td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td class="cell tableheader">TVA 21% : </td>
                <td class="cell infoprix">{{ $facture['sale']['amount_tva'] }} €</td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td class="cell tableheader">TOTAL :</td>
                <td class="cell infoprix">{{ $facture['sale']['total_amount'] }} €</td>
            </tr>
        </tfoot>
    </table>
    <footer>
        <table class="footer-table">
            <tr>
                <td class="footer-left">
                    CESAROSALIE SRL
                </td>
                <td class="footer-center">
                    Pour acquit
                    <br>
                    TVA BE0685.378.174
                </td>
                <td class="footer-right">
                    Banque
                    <br>
                    BE08 0019 6561 9013
                </td>
            </tr>
        </table>
    </footer>
</body>
</html>