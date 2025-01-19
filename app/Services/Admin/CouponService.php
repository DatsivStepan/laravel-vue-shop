<?php

namespace App\Services\Admin;

use App\Models\Coupons;

interface CouponService
{
    /**
     * Generate coupon code before create coupon
     *
     * @param int|null $len
     * @param int|null $sectionSize
     *
     * @return string
     */
    public function generateCode(int $len = null, int $sectionSize = null): string;

    /**
     * Used for create coupon
     *
     * @param array $data
     *
     * @return Coupons
     */
    public function create(array $data): Coupons;
}
