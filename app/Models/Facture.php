<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Sale;
use App\Models\Client;


class Facture extends Model
{

    protected $fillable = [
		'date_facture',
		'send',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

}
