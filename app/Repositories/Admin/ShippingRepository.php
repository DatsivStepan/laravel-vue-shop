<?php

namespace App\Repositories\Admin;

use App\Models\Shipping;
use App\Repositories\BaseRepository;
use Spatie\QueryBuilder\AllowedFilter;

class ShippingRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return Shipping::class;
    }

    /**
     * @param null|int $perPage
     * @return AllowedFilter
     */
    public function getWithQuery($perPage = null)
    {
        $filters = [
            AllowedFilter::scope('search'),
            AllowedFilter::scope('products_vendors'),
        ];

        return $this->getWithQueryBuilder(
            $filters,
            [
                'order_id', 'name', 'email', 'address',
                'city', 'state', 'zip_code', 'phone', 'note'
            ],
            $perPage
        );
    }
}
