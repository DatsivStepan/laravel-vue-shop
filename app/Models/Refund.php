<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'data' => 'array',
    ];
}
