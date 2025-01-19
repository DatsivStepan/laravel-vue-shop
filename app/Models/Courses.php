<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Courses
 *
 * @property int $id
 * @property int $course_id
 * @property int $category_id
 * @property string $name
 * @property string $code
 * @property string $start_date
 * @property string $end_date
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Courses extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'courses';

    protected $fillable = [
        'course_id',
        'category_id',
        'name',
        'code',
        'start_date',
        'end_date'
    ];

    /**
     * @return bool
     */
    public function isOver()
    {
        if (!$this->end_date) {
            return false;
        }

        return Carbon::parse($this->end_date)->getTimestamp() < Carbon::now()->getTimestamp();
    }

    /**
     * Get the App\Models\ProductCourses record associated with the Courses.
     */
    public function productsAssigns()
    {
        return $this->hasMany(ProductCourses::class, 'course_id', 'id');
    }
}
