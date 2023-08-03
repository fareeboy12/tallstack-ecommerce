<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Products; 

class Carts extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'total',
        'coupon_code',
        'coupon_price',
        'shipping_method',
        'shipping_fee',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class);
    }
}
