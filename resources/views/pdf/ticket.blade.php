<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ticket de caisse</title>
    <style>
        /* Styles CSS pour le ticket */
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 400px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .info {
            margin-bottom: 10px;
        }
        .info span {
            font-weight: bold;
        }
        .products {
            margin-bottom: 20px;
        }
        .product {
            margin-bottom: 10px;
        }
        .total {
            font-size: 16px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 class="title">Ticket de caisse</h2>
            <div class="info">
                <span>Date et heure :</span> {{ $sale->created_at }}
            </div>
            <div class="info">
                <span>Client :</span> {{ $sale->client->name ?? 'Non spécifié' }}
            </div>
        </div>
        <div class="products">
            <h3>Produits :</h3>
            @foreach ($sale->products as $product)
                <div class="product">
                    <div>{{ $product->name }}</div>
                    <div>Prix unitaire : {{ $product->pivot->price }} €</div>
                    <div>Quantité : {{ $product->pivot->quantity }}</div>
                </div>
            @endforeach
        </div>
        <div class="total">
            <span>Total :</span> {{ $sale->total_amount }} €
        </div>
    </div>
</body>
</html>
