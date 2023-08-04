<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Products;
use App\Models\Orders;

class OrderItems extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class);
    }

    public function order()
    {
        return $this->belongsTo(Orders::class);
    }
}
