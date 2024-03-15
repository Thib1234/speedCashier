<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'total_amount',
        'cash',
        'bancontact',
        'credit_card',
        'virement',
    ];
    

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
