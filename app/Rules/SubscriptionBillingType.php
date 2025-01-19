<?php

namespace App\Rules;

use App\Classes\Enum\Order\BillingType;
use App\Models\Products;
use Illuminate\Contracts\Validation\Rule;

class SubscriptionBillingType implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Products::query()->whereKey($value)
            ->where('billing_type', BillingType::SUBSCRIPTION)
            ->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
