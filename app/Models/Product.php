<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'barcode',
        'name',
        'category',
        'stock',
        'min_stock',
        'unit',
        'cost_price',
        'supplier_name',
    ];

    protected $appends = ['status', 'status_badge_class', 'selling_price'];

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function units()
    {
        return $this->hasMany(ProductUnit::class)->orderBy('sort_order');
    }

    public function priceTiers()
    {
        return $this->hasManyThrough(PriceTier::class, ProductUnit::class);
    }

    /**
     * Get base unit (sort_order = 0)
     */
    public function baseUnit()
    {
        return $this->hasOne(ProductUnit::class)->where('sort_order', 0);
    }

    /**
     * Computed selling_price from base unit's retail price (for backward compat)
     */
    public function getSellingPriceAttribute()
    {
        $base = $this->relationLoaded('units')
            ? $this->units->firstWhere('sort_order', 0)
            : $this->baseUnit;

        if ($base && $base->priceTier) {
            return (float) $base->priceTier->price_retail;
        }
        return 0;
    }

    public function getStatusAttribute()
    {
        if ($this->stock <= 0) {
            return 'Habis';
        } elseif ($this->stock <= $this->min_stock) {
            return 'Menipis';
        }
        return 'Aman';
    }

    public function getStatusBadgeClassAttribute()
    {
        if ($this->stock <= 0) {
            return 'bg-red-100 text-red-700 font-semibold px-2.5 py-0.5 rounded-full text-xs inline-flex items-center gap-1';
        } elseif ($this->stock <= $this->min_stock) {
            return 'bg-amber-100 text-amber-700 font-semibold px-2.5 py-0.5 rounded-full text-xs inline-flex items-center gap-1';
        }
        return 'bg-emerald-100 text-emerald-700 font-semibold px-2.5 py-0.5 rounded-full text-xs inline-flex items-center gap-1';
    }
}
