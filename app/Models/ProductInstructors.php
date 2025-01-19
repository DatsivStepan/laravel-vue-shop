<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductInstructors
 *
 * @property int $id
 * @property int $product_id
 * @property int $instructor_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ProductInstructors extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'product_instructors';

    protected $fillable = [
        'product_id',
        'instructor_id'
    ];

    /**
     * Get the App\Models\Categories record associated with the ProductCategories.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'instructor_id');
    }

    /**
     * Get the App\Models\Products record associated with the ProductCategories.
     */
    public function product()
    {
        return $this->hasOne(Products::class, 'id', 'product_id');
    }
}
