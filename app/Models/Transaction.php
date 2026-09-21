<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'payment_method',
        'total_amount',
        'paid_amount',
        'change_amount',
        'cashier_name',
        'customer_name',
        'customer_id',
        'sale_mode',
        'status',
        'notes',
    ];

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'success'  => 'bg-emerald-100 text-emerald-700',
            'refunded' => 'bg-amber-100 text-amber-700',
            'void'     => 'bg-red-100 text-red-700',
            default    => 'bg-slate-100 text-slate-700',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'success'  => 'Berhasil',
            'refunded' => 'Refund',
            'void'     => 'Dibatalkan',
            default    => $this->status,
        };
    }

    public function getPaymentIconAttribute()
    {
        return match($this->payment_method) {
            'Tunai'    => 'banknote',
            'QRIS'     => 'qr-code',
            'Transfer' => 'send',
            'Debit'    => 'credit-card',
            default    => 'wallet',
        };
    }

    public function getSaleModeLabelAttribute()
    {
        return $this->sale_mode === 'grosir' ? 'Grosir' : 'Eceran';
    }
}
