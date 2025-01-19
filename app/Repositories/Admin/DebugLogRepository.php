<?php

namespace App\Repositories\Admin;

use App\Events\Coupons\Created;
use App\Events\Coupons\Updated;
use App\Models\DebugLog;
use App\Repositories\BaseRepository;

class DebugLogRepository extends BaseRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return DebugLog::class;
    }

    /**
     * @param array $data
     *
     * @return Model
     */
    public function create(array $data)
    {
        $debug = parent::create($this->prepare($data));

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
     * It need for check that this code not use in coupons, when we generate new code
     *
     * @return Collection
     */
    public function getAllCodesOfCoupons(): Collection
    {
        return Coupons::query()->pluck('code');
    }
}
