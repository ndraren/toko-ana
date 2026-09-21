<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'type',
        'notes',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getTypeLabelAttribute()
    {
        return $this->type === 'reseller' ? 'Reseller' : 'Retail';
    }

    public function getTypeBadgeClassAttribute()
    {
        return $this->type === 'reseller'
            ? 'bg-purple-100 text-purple-700'
            : 'bg-blue-100 text-blue-700';
    }
}
