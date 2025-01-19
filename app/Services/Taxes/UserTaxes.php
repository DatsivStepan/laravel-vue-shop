<?php

namespace App\Services\Taxes;

use App\Classes\Enum\Settings\TaxSettings;
use App\Models\Taxes\AdvancedTaxesField;
use App\Models\User;

class UserTaxes implements Tax
{
    /**
     * @param User $user
     * @return float
     */
    public function calculate(User $user): float
    {
        if (getSetting(TaxSettings::ENABLE_TAXES)) {
            if (getSetting(TaxSettings::ENABLE_ADVANCE_TAXES)) {
                $fields = AdvancedTaxesField::query()
                    ->where('model_class', User::class)
                    ->with('values')
                    ->get();

                if ($fields->count()) {
                    $taxPercent = 0;

                    /** @var AdvancedTaxesField $field */
                    foreach ($fields as $field) {
                        $taxPercent += $field->values()
                            ->sum('tax');
                    }

                    if ($taxPercent) {
                        return floatval($taxPercent);
                    } else {
                        if (getSetting(TaxSettings::ENABLE_GLOBAL_TAX_VALUE)) {
                            return floatval(getSetting(TaxSettings::TAX_VALUE));
                        }
                    }
                }
            }

            return floatval(getSetting(TaxSettings::TAX_VALUE));
        }

        return floatval(0);
    }
}
