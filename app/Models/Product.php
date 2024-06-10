<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Sale;
use App\Models\Category;
use App\Models\Acompte;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'price_htva',
        'stock',
        'purchase_price',
        'active',
        'category_id'
    ];

    public function sales(): BelongsToMany
    {
        return $this->belongsToMany(Sale::class, 'product_sale')
            ->withPivot('quantity', 'price', 'total', 'total_htva');
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function acomptes()
    {
        return $this->hasMany(Acompte::class);
    }
    
}
