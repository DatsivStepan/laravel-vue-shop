<?php

namespace App\Repositories\Admin\Sales;

use App\Models\OrdersProductSeatsUsed;
use App\Repositories\BaseRepository;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SeatsDetailsRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return OrdersProductSeatsUsed::class;
    }

    /**
     * @param null|int $perPage
     * @param int $seatId
     * @return AllowedFilter
     */
    public function getWithQuery(int $seatId, $perPage = null)
    {
        return QueryBuilder::for(OrdersProductSeatsUsed::class)
            ->where('order_product_seats_id', $seatId)
            ->allowedFilters([
                AllowedFilter::scope('search'),
            ])
            ->allowedSorts([
                'order_id',
                'order_product_seats_id',
                'status'
            ])
            ->paginate($perPage);
    }
}
