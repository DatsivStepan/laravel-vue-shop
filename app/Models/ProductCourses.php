<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductCourses
 *
 * @property int $id
 * @property int $product_id
 * @property int $course_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ProductCourses extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'product_courses';

    protected $fillable = [
        'product_id',
        'course_id',
    ];

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeSearch($query, $value)
    {
        return $query->whereHas('course', function ($query) use ($value) {
            $query->where('name', 'like', '%' . $value . '%');
        });
    }

    /**
     * @return string
     */
    public function getCourseName()
    {
        return $this->course ? $this->course->name : null;
    }

    /**
     * Get the App\Models\Products record associated with the ProductCourses.
     */
    public function product()
    {
        return $this->hasOne(Products::class, 'id', 'product_id');
    }

    /**
     * Get the App\Models\Courses record associated with the ProductCourses.
     */
    public function course()
    {
        return $this->hasOne(Courses::class, 'id', 'course_id');
    }
}
