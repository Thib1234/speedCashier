<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Sale;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'stock',
        'purchase_price',
        'active'
    ];

    public function sales(): BelongsToMany
    {
        return $this->belongsToMany(Sale::class, 'product_sale')
            ->withPivot('quantity', 'price');
    }
    
}
