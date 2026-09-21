<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_unit_id',
        'price_retail',
        'price_wholesale',
        'min_wholesale_qty',
    ];

    public function productUnit()
    {
        return $this->belongsTo(ProductUnit::class);
    }
}
