<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'tva',
        'company',
        'adresse',
        'code_postal',
        'ville',
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function acomptes(): HasMany
{
    return $this->hasMany(Acompte::class);
}

public function factures(): HasMany
{
    return $this->hasMany(Facture::class);
}

}
