<?php

namespace Tests\Unit\Services\Taxes;

use App\Classes\Enum\Settings\TaxSettings;
use App\Models\Settings;
use App\Models\Taxes\AdvancedTaxesField;
use App\Models\Taxes\AdvancedTaxesValue;
use App\Models\User;
use App\Services\Taxes\UserTaxes;
use Tests\TestCase;

class TaxTest extends TestCase
{
    public $tenancy = true;

    public function testTax()
    {
        $this->seed(\AddTaxesSettings::class);
        Settings::query()->whereIn('key', [TaxSettings::ENABLE_TAXES, TaxSettings::ENABLE_ADVANCE_TAXES])->update([
            'value' => 1,
        ]);

        $user = factory(User::class)->create();
        factory(UserData::class)->create([
            'user_id' => $user->id,
            'first_name' => 'test',
        ]);

        $field = factory(AdvancedTaxesField::class)->create([
            'key' => 'first_name',
            'name' => 'First Name',
        ]);
        factory(AdvancedTaxesValue::class)->create([
            'field_id' => $field->id,
            'value' => 'test',
            'tax' => 10,
        ]);

        $service = new UserTaxes();

        $this->assertEquals(10, $service->calculate($user));
    }
}
