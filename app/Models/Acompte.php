<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acompte extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', 'montant', 'date', 'status'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
