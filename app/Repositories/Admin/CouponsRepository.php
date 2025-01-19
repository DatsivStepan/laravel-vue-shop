<?php

namespace App\Repositories\Admin;

use App\Events\Coupons\Created;
use App\Events\Coupons\Updated;
use App\Models\Coupons;
use App\Repositories\BaseRepository;
use Exception;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\AllowedFilter;

class CouponsRepository extends BaseRepository
{
    /**
     * @return Coupons
     */
    protected function getModelClass(): string
    {
        return Coupons::class;
    }

    /**
     * @param array $data
     *
     * @return Coupons
     * @throws Exception
     *
     */
    public function create(array $data): Coupons
    {
        /** @var Coupons $coupon */
        $coupon = parent::create($this->prepare($data));

        // Trigger event.
        Created::dispatch($coupon);

        return $coupon;
    }

    /**
     * @param Coupons $coupon
     * @param array $data
     *
     * @return Coupons
     * @throws Exception
     *
     */
    public function update($coupon, array $data): Coupons
    {
        /** @var Coupons $coupon */
        $coupon = parent::update($coupon, $this->prepare($data));

        // Trigger event.
        Updated::dispatch($coupon);

        return $coupon;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function prepare($data): array
    {
        $data['timestart'] = (!empty($data['timestart'])) ? strtotime($data['timestart']) : 0;
        $data['timeend'] = (!empty($data['timeend'])) ? strtotime($data['timeend']) : 0;
        $data['usedperuser'] = (!empty($data['usedperuser'])) ? $data['usedperuser'] : 0;
        $data['usedcount'] = (!empty($data['usedcount'])) ? $data['usedcount'] : 0;

        return $data;
    }

    /**
     * It need for check that this code not use in coupons, when we generate new code
     *
     * @return Collection
     */
    public function getAllCodesOfCoupons(): Collection
    {
        return Coupons::query()->pluck('code');
    }

    public function findByCode($code, $with = [])
    {
        return $this->getModelClass()::query()->with($with)->where('code', $code)->first();
    }

    /**
     * @param User $user
     * @param int $perPage
     * @return AllowedFilter
     */
    public function getWithQuery($user = null, $perPage = 10)
    {
        $filters = [
            AllowedFilter::scope('search'),
            $this->allowedFilterStatus(null, 'status', $user),
        ];

        return $this->getWithQueryBuilder(
            $filters,
            [
                'created_at', 'code', 'timestart', 'timeend', 'discount'
            ],
            $perPage
        );
    }
}
