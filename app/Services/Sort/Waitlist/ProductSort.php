<?php
namespace App\Services\Sort\Waitlist;

use Illuminate\Database\Eloquent\Builder;
use \Spatie\QueryBuilder\Sorts\Sort;

class ProductSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        $query
            ->join('products', 'product_waitlist.product_id', '=', 'products.id')
            ->orderBy('products.name', $direction);
    }
}
?>
