<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



class ProductSale extends Model
{
	protected $table = 'product_sale'; // Spécifiez le nom de la table
	
    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'price',
        'tax_amount',
    ];

    // Relation Many-to-One avec le modèle Sale
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    // Relation Many-to-One avec le modèle Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
