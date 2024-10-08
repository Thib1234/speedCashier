<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Facture;
use App\Models\ProductSale;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'datetime',
        'cash',
        'bancontact',
        'credit_card',
        'virement',
        'stripe',
        'payconiq',
        'total_amount',
        'client_id',
        'montant_total_htva',
        'amount_tva',
    ];
    public function productSales(): HasMany
    {
        return $this->hasMany(ProductSale::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_sale')
            ->withPivot('quantity', 'price', 'total', 'total_htva');
    }
    public function facture(): HasOne
    {
        return $this->hasOne(Facture::class);
    }
}