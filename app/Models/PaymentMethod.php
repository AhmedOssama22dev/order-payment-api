<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
