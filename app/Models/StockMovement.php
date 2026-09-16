<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'unit',
        'source_category',
        'notes',
        'user_name',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getFormattedChangeAttribute()
    {
        $sign = in_array($this->type, ['in']) ? '+' : '-';
        return "{$sign} {$this->quantity} {$this->unit}";
    }

    public function getChangeBadgeClassAttribute()
    {
        switch ($this->type) {
            case 'in':
                return 'bg-emerald-100 text-emerald-800 font-bold px-3 py-1 rounded-lg text-sm';
            case 'damaged':
                return 'bg-red-100 text-red-800 font-bold px-3 py-1 rounded-lg text-sm';
            case 'return':
                return 'bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded-lg text-sm';
            case 'out':
            default:
                return 'bg-gray-100 text-gray-800 font-bold px-3 py-1 rounded-lg text-sm';
        }
    }
}
