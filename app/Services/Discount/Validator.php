<?php

namespace App\Services\Discount;

use App\Models\Discount;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class Validator
{
    /**
     * @param Discount $discount
     * @param User $user
     *
     * @return bool
     */
    public function validateUsedPerUser(Discount $discount, User $user): bool
    {
        $timesUsedDiscount = $user->orders()->whereHas('discounts', function (Builder $query) use ($discount) {
            $query->whereKey($discount->id);
        })->count();

        return $discount->used_per_user > $timesUsedDiscount;
    }

    /**
     * @param Discount $discount
     *
     * @return bool
     */
    public function validateMaxApplied(Discount $discount): bool
    {
        return $discount->appliedProducts() < $discount->max_applied_products;
    }

    /**
     * @param Discount $discount
     * @param User $user
     *
     * @return bool
     */
    public function validate(Discount $discount, User $user): bool
    {
        return $this->validateUsedPerUser($discount, $user) && $this->validateMaxApplied($discount);
    }
}
