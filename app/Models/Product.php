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
        'selling_price',
        'wholesale_price',
        'wholesale_min_qty',
        'pack_name',
        'pack_price',
        'pack_qty',
        'box_name',
        'box_price',
        'box_qty',
        'supplier_name',
    ];

    protected $appends = ['status', 'status_badge_class'];

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
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
