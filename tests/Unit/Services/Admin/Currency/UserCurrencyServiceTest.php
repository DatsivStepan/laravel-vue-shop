<?php

namespace Tests\Unit\Services\Admin\Currency;

use App\Models\User;
use App\Services\Admin\User\UserSettingsService;
use Tests\TestCase;

class UserCurrencyServiceTest extends TestCase
{
    protected $tenancy = true;

    public function testUserSelectCurrency()
    {
        $user = factory(User::class)->create();
        $code = 'USD';

        $service = new UserSettingsService();

        $service->selectCurrency($user, $code);

        $this->assertEquals($code, $user->settings()->get('currency'));
    }
}
