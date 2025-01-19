<?php

namespace App\Repositories\Admin\Sales;

use App\Classes\Enum\ModelStatus;
use App\Models\OrdersProducts;
use App\Models\OrdersProductSeats;
use App\Repositories\BaseRepository;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SeatsRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return OrdersProductSeats::class;
    }

    /**
     * @param null|int $perPage
     * @return AllowedFilter
     */
    public function getWithQuery($perPage = null)
    {
        return QueryBuilder::for(OrdersProductSeats::class)
            // ->whereDoesntHave('seatUsed')
            ->allowedFilters([
                AllowedFilter::scope('search'),
                AllowedFilter::scope('paid_on_between'),
                'status'
            ])
            ->allowedSorts([
                'order_product_id', 'seat_key', 'status',
                'expiration',
            ])
            ->paginate($perPage);
    }

    /**
     * @param null|int $perPage
     * @return AllowedFilter
     */
    /*
    public function getWithQuery($perPage = null)
    {
        return QueryBuilder::for(OrdersProducts::class)
            ->allowedFilters([
                AllowedFilter::scope('search'),
                AllowedFilter::scope('seats'),
            ])
            ->allowedSorts([
                'order_id', 'product_id', 'price', 'discount_price', 'discount',
                'name', 'quantity', 'enrolled'
            ])
            ->paginate($perPage);
    }
    */

    // ACTIONS

    /**
     * @param array|int $ids
     * @return bool
     */
    public function hide($ids)
    {
        $ids = is_array($ids) ? $ids : [$ids];
        return $this->updateIn($ids, ['status' => ModelStatus::INACTIVE]);
    }

    /**
     * @param array|int $ids
     * @return bool
     */
    public function show($ids)
    {
        $ids = is_array($ids) ? $ids : [$ids];
        return $this->updateIn($ids, ['status' => ModelStatus::ACTIVE]);
    }
    // ACTIONS
}
