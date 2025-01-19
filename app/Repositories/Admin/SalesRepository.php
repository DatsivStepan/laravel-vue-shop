<?php

namespace App\Repositories\Admin;

use App\Classes\Enum\Order\PaymentStatus;
use App\Models\Orders;
use App\Repositories\BaseRepository;
use App\Services\Sort\DefaultSort;
use App\Services\Sort\Sales\CustomerSort;
use App\Services\Sort\Sales\QuantitySort;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class SalesRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return Orders::class;
    }

    /**
     * @param int $perPage
     * @param int $paymentType
     *
     * @return LengthAwarePaginator
     */
    public function getWithPagination(int $perPage = 10, $paymentType = null)
    {
        $query = QueryBuilder::for(Orders::class)
            ->allowedFilters([
                AllowedFilter::scope('paid_on_between'),
                AllowedFilter::scope('products_vendors'),
                AllowedFilter::scope('search'),
                'payment_status',
                'billing_type'
            ])
            ->allowedSorts([
                'id',
                'created_at',
                'amount',
                'subtotal',
                'payment_status',
                'discount',
                'status',
                'note',
                AllowedSort::custom('customer', new CustomerSort()),
                AllowedSort::custom('quantity', new QuantitySort())->defaultDirection('desc'),
            ])
            ->whereIn('payment_status',
                [
                    PaymentStatus::PAYMENT_STATUS_PENDING,
                    PaymentStatus::PAYMENT_STATUS_COMPLETED,
                    PaymentStatus::PAYMENT_STATUS_REFUND,
                    PaymentStatus::PAYMENT_STATUS_REFUNDED_PARTIAL,
                    PaymentStatus::PAYMENT_STATUS_REJECTED,
                ])
                ->defaultSort(AllowedSort::custom('created_at', new DefaultSort())->defaultDirection('desc'));

        if ($paymentType) {
            $query->where('payment_status', $paymentType);
        }

        return $query->paginate($perPage);
    }
}
