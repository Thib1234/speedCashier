<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Sale;


class Facture extends Model
{

    protected $fillable = [
		'date_facture',
		'send',
    ];

    public function sale(): HasOne
    {
        return $this->hasOne(Sale::class);
    }

}
