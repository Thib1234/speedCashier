<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Sale;
use App\Models\Category;
use App\Models\Product;

class Acompte extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'stock',
        'purchase_price',
        'active',
        'category_id',
        'product_id'
    ];

    public function sales(): BelongsToMany
    {
        return $this->belongsToMany(Sale::class, 'product_sale')
            ->withPivot('quantity', 'price', 'total');
    }
    public function category()
    {
        return $this->belongsTo(Category::class); // Relation belongsTo avec Category
    }
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

}
