<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $guarded = ['id'];

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeSearch($query, $value)
    {
        return $query->where(function ($query) use ($value) {
            $query->orWhere('name', 'like', '%' . $value . '%');
            $query->orWhere('code', 'like', '%' . $value . '%');
        });
    }
}


