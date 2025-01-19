<?php

namespace App\Repositories\Admin;

use App\Classes\Enum\Order\EnrolledType;
use App\Models\Orders;
use App\Models\OrdersProducts;
use App\Repositories\BaseRepository;
use App\Services\EnrollService;
use Spatie\QueryBuilder\AllowedFilter;

class ScheduledEnrollmentsRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return OrdersProducts::class;
    }

    /**
     * @param array $ids
     *
     * @return AllowedFilter
     */
    public function deleteEnroll(array $ids)
    {
        return $this->updateIn($ids, [
            'enrolled' => EnrolledType::DELETED
        ]);
    }

    /**
     * @param int $id
     *
     * @return AllowedFilter
     */
    public function enroll($id)
    {
        if (!$orderProduct = $this->getOne($id)) {
            return null;
        }

        if (!$order = Orders::find($orderProduct->order_id)) {
            return null;
        }

        if ((new EnrollService())->enrollForOrder($order, $orderProduct->product_id) !== false) {
            return $this->updateIn([$id], [
                'enrolled' => EnrolledType::ENROLLED
            ]);
        }

        return null;
    }

    /**
     * @return AllowedFilter
     */
    public function getPastNotEnrolled()
    {
        return OrdersProducts::scheduledNotEnrollment(EnrolledType::NOT_ENROLLED)->get();
    }

    /**
     * @param null|int $perPage
     * @return AllowedFilter
     */
    public function getWithQuery($perPage = null)
    {
        $filters = [
            AllowedFilter::scope('search'),
            AllowedFilter::scope('scheduled_enrollment'),
            AllowedFilter::scope('paid_on_between'),
        ];

        return $this->getWithQueryBuilder(
            $filters,
            [
                'order_id', 'product_id', 'price', 'discount_price', 'discount',
                'name', 'quantity', 'enrolled'
            ],
            $perPage
        );
    }
}
