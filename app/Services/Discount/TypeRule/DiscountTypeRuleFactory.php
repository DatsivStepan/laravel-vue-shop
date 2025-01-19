<?php

namespace App\Services\Discount\TypeRule;

use App\Classes\Enum\DiscountType;
use Exception;

class DiscountTypeRuleFactory
{
    /**
     * @param $type
     *
     * @return AllProducts|AnyProduct|Condition
     * @throws Exception
     *
     */
    public static function make($type): DiscountTypeRule
    {
        switch ($type) {
            case DiscountType::ALL:
                return new AllProducts();
            case DiscountType::ANY:
                return new AnyProduct();
            case DiscountType::CONDITION:
                return new Condition();
            default:
                throw new Exception(__('errors.unknown_discount_type'));
        }

    }
}
