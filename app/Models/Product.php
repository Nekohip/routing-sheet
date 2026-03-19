<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_code',
        'sales_rep',
        'status',
    ];

    public function processes()
    {
        return $this->hasMany(ProductProcess::class)->orderBy('sequence');
    }
}
