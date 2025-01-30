<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'payment_status',
        'payment_method_id'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    // has one payment method
    public function paymentMethod()
    {
        return $this->hasOne(PaymentMethod::class);
    }

}
