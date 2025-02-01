<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'status',
    ];

    protected $guarded = ['total_price', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($order) {
            if ($order->isDirty('user_id')) {
                throw new \Exception("User ID cannot be updated.");
            }
        });
    }

}

