<?php

namespace App\Rules;

use App\Models\Coupons;
use App\Models\OrdersProductSeats;
use Illuminate\Contracts\Validation\Rule;
use ReflectionException;

class CouponSeatsExists implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param string $value
     *
     * @return bool
     * @throws ReflectionException
     *
     */
    public function passes($attribute, $value)
    {
        return Coupons::where('code', $value)->exists() ||
                OrdersProductSeats::where('seat_key', $value)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     * @throws ReflectionException
     *
     */
    public function message()
    {
        return 'The selected coupon code is invalid.';
    }
}
