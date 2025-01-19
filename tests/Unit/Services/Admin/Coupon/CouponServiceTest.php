<?php

namespace Tests\Unit\Services\Admin\Coupon;

use App\Repositories\Admin\CouponsRepository;
use App\Services\Admin\Coupon\CouponService;
use Tests\TestCase;

class CouponServiceTest extends TestCase
{
    /** @var CouponService */
    private $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->mock(CouponsRepository::class, function ($mock) {
            $mock->shouldReceive('getAllCodesOfCoupons')->once()->andReturn(collect());
        });
        $this->service = app(CouponService::class);
    }

    public function testGenerateDefaultCodeMethod()
    {
        $code = $this->service->generateCode();

        $this->assertEquals(19, strlen($code));
        $this->assertEquals(1, preg_match('/^[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/', $code));
    }
}
