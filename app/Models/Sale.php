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
        'total_amount',
        'client_id',
    ];
    public function productSales(): HasMany
    {
        return $this->hasMany(ProductSale::class);
    }


    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_sale')
            ->withPivot('quantity', 'price', 'total');
    }
}