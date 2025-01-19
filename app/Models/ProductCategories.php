<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductCategories
 *
 * @property int $id
 * @property int $product_id
 * @property int $category_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ProductCategories extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'product_categories';

    protected $fillable = [
        'product_id',
        'category_id'
    ];

    /**
     * Get the App\Models\Categories record associated with the ProductCategories.
     */
    public function category()
    {
        return $this->hasOne(Categories::class, 'id', 'category_id');
    }

    /**
     * Get the App\Models\Products record associated with the ProductCategories.
     */
    public function product()
    {
        return $this->hasOne(Products::class, 'id', 'product_id');
    }
}
