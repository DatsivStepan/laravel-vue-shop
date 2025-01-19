<?php
namespace App\Services\Sort\Products;

use Illuminate\Database\Eloquent\Builder;
use \Spatie\QueryBuilder\Sorts\Sort;

class CoursesSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        $query->withCount('courses')->orderBy('courses_count', $direction);
    }
}
?>
