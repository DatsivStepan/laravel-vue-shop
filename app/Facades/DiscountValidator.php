<?php

namespace App\Facades;

use App\Models\Discount;
use App\Models\User;
use App\Services\Discount\Validator;
use Illuminate\Support\Facades\Facade;

/**
 * Class DiscountValidatorFacade
 * @method static Validator validate(Discount $discount, User $user)
 * @method static Validator validateMaxApplied(Discount $discount)
 *
 * @package App\Services\Discount
 */
class DiscountValidator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'DiscountValidator';
    }
}
