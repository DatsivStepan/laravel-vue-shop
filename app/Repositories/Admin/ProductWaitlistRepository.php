<?php

namespace App\Repositories\Admin;

use App\Models\ProductWaitlist;
use App\Repositories\BaseRepository;
use App\Services\Sort\DefaultSort;
use App\Services\Sort\Waitlist\CustomerSort;
use App\Services\Sort\Waitlist\ProductSort;
use Exception;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class ProductWaitlistRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return ProductWaitlist::class;
    }

    /**
     * @param null|int $perPage
     * @return AllowedFilter
     */
    public function getWithQuery($perPage = null)
    {
        $filters = [
            AllowedFilter::scope('search'),
            'user_id',
            'created_at',
        ];

        return $this->getWithQueryBuilder(
            $filters,
            [
                'created_at',
                AllowedSort::custom('customer', new CustomerSort())->defaultDirection('desc'),
                AllowedSort::custom('product', new ProductSort())->defaultDirection('desc')
            ],
            $perPage,
            AllowedSort::custom('created_at', new DefaultSort())->defaultDirection('desc')
        );
    }

    /**
     * @param array $waitlists
     * @return bool
     */
    public function sendNotifications($waitlists): bool
    {
        DB::beginTransaction();
        try {
            foreach ($waitlists as $waitlist) {
                if (!$sendNotification = false) { //TODO functional sent notification
                    DB::rollback();
                    return false;
                }

                $waitlist->sent = ProductWaitlist::STATUS_SENT;
                if (!$waitlist->save()) {
                    DB::rollback();
                    return false;
                }
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param int $productId
     * @param int $userId
     * @return bool
     */
    public function addProductToWaitlist(int $productId, int $userId): bool
    {
        $conditions = [
            'user_id' => $userId,
            'product_id' => $productId,
        ];

        if (!$this->getOneByConditions($conditions)) {
            $this->create($conditions);
            return true;
        }
        return false;
    }

    /**
     * @param array $ids
     * @return bool
     */
    public function deleteAll(array $ids)
    {
        return ($this->getModelClass())::query()
            ->whereIn('id', $ids)->delete();
    }
}
