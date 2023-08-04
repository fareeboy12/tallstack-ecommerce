<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; 

class Orders extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'order_number',
        'first_name',
        'last_name',
        'company',
        'country',
        'address1',
        'address2',
        'city',
        'state',
        'postcode',
        'phone',
        'email',
        'notes',
        'subtotal',
        'shipping_fee',
        'coupon_price',
        'total',
        'payment_method',
        'payment_status',
        'order_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
