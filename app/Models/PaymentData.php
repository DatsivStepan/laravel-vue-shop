<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentData extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'data' => 'array',
    ];
    protected $table = 'payment_data';
}
