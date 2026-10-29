<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'unit_name',
        'conversion_factor',
        'sort_order',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function priceTier()
    {
        return $this->hasOne(PriceTier::class);
    }

    /**
     * Get the appropriate price based on mode and quantity.
     */
    public function getPriceFor(string $mode = 'eceran', int $qty = 1): float
    {
        $tier = $this->priceTier;
        if (!$tier) return 0;

        if ($mode === 'grosir') {
            return (float) $tier->price_wholesale > 0 ? (float) $tier->price_wholesale : (float) $tier->price_retail;
        }

        // Eceran mode: check if qty meets min_wholesale_qty for auto-grosir
        if ($tier->min_wholesale_qty > 0 && $qty >= $tier->min_wholesale_qty && $tier->price_wholesale > 0) {
            return (float) $tier->price_wholesale;
        }

        return (float) $tier->price_retail;
    }
}
