<?php
namespace App\Services\Sort\Waitlist;

use Illuminate\Database\Eloquent\Builder;
use \Spatie\QueryBuilder\Sorts\Sort;

class CustomerSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        $query
            ->join('users', 'product_waitlist.user_id', '=', 'users.id')
            ->orderBy('users.name', $direction);
    }
}
?>
