<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'color'];

    public function products()
    {
        return $this->hasMany(Product::class); // Relation hasMany avec Product
    }
}
