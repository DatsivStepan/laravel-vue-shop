<?php

namespace Tests\Feature;

use App\Models\Settings;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class RefundsTest extends TestCase
{
    use WithoutMiddleware;
    use WithFaker;

    protected $tenancy = true;
    protected $createUser = true;

    public function testGetRefundReasons()
    {
        $this->seed(\AddRefundSettings::class);
        Settings::query()->where('key', 'refund_reasons')->update([
            'value' => "one\ntwo",
        ]);

        $this->apiAs('GET', route('api.admin.refund.reasons'))
            ->assertStatus(200);
    }

}
