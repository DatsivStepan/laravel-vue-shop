<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductCourses
 *
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ProductFavorites extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'product_favorites';

    protected $fillable = [
        'user_id',
        'product_id'
    ];

    /**
     * @return string
     */
    public function getProductName()
    {
        return $this->product ? $this->product->name : null;
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeSearch($query, $value)
    {
        return $query->whereHas('product', function ($query) use ($value) {
            $query->where('name', 'like', '%' . $value . '%');
        });
    }

    /**
     * Get the App\Models\Products record associated with the ProductCourses.
     */
    public function product()
    {
        return $this->hasOne(Products::class, 'id', 'product_id');
    }
}
