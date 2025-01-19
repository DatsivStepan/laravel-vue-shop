<?php
namespace App\Services\Sort;

use Illuminate\Database\Eloquent\Builder;
use \Spatie\QueryBuilder\Sorts\Sort;

class DefaultSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';
        $query->orderByRaw("`{$property}` {$direction}");
    }
}
?>
