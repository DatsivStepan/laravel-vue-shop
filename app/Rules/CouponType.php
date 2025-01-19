<?php

namespace App\Rules;

use App\Classes\Enum\CouponType as CouponTypeEnum;
use Illuminate\Contracts\Validation\Rule;
use ReflectionException;

class CouponType implements Rule
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
        return CouponTypeEnum::exists($value);
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
        return 'The :attribute must be as ' . implode(', ', CouponTypeEnum::getAll());
    }
}
