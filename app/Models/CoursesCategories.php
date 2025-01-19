<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CoursesCategories
 *
 * @property int $id
 * @property int $course_id
 * @property string $name
 * @property int $parent_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class CoursesCategories extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'courses_categories';

    protected $fillable = [
        'category_id',
        'name',
        'parent_id'
    ];

    /**
     * Get the App\Models\ProductCourses record associated with the Courses.
     */
    public function productsAssigns()
    {
        return $this->hasMany(ProductCourses::class, 'course_id', 'id');
    }
}
