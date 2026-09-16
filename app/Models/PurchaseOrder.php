<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'po_number',
        'supplier_name',
        'quantity',
        'unit',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
