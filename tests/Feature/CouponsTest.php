<?php

namespace Tests\Feature;

use App\Classes\Enum\CouponType;
use App\Http\Resources\Admin\CouponResource;
use App\Models\Coupons;
use App\Models\Products;
use App\Models\Vendors;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Str;
use Tests\TestCase;

class CouponsTest extends TestCase
{
    use WithoutMiddleware;
    use WithFaker;

    protected $tenancy = true;
    protected $createUser = true;

    public function testSuccessCreateCoupon()
    {
        $coupon = [
            'code' => $this->faker->unique()->word,
            'discount' => $this->faker->numberBetween(0, 100),
            'timeend' => Carbon::tomorrow()->format(__('langconfig.iso8601')),
            'timestart' => Carbon::yesterday()->format(__('langconfig.iso8601')),
            'type' => CouponType::CURRENCY,
        ];

        $this->apiAs('POST', route('api.coupons.store'), $coupon)
            ->assertStatus(201)
            ->assertJsonFragment($coupon);
    }

    public function testSuccessCreateCouponWithoutCode()
    {
        $coupon = [
            'discount' => $this->faker->numberBetween(0, 100),
        ];

        $this->apiAs('POST', route('api.coupons.store'), $coupon)
            ->assertStatus(201)
            ->assertJsonFragment($coupon);
    }

    public function testFailCreateCouponWhenCouponWithThisCodeAlreadyExist()
    {
        $code = Str::random(16);

        factory(Coupons::class)->create(['code' => $code]);

        $coupon = [
            'discount' => $this->faker->numberBetween(0, 100),
            'code' => $code,
        ];

        $this->apiAs('POST', route('api.coupons.store'), $coupon)
            ->assertStatus(422);
    }

    public function testValidationFailCreateCoupon()
    {
        $this->apiAs('POST', route('api.coupons.store'), [])
            ->assertStatus(422);
    }

    public function testSuccessUpdateCoupon()
    {
        $coupon = factory(Coupons::class)->create();
        $updateCouponData = [
            'code' => $this->faker->unique()->word,
            'discount' => $this->faker->numberBetween(0, 100),
        ];

        $this->apiAs('PUT', route('api.coupons.update', $coupon->id), $updateCouponData)
            ->assertStatus(200)
            ->assertJsonFragment($updateCouponData);
    }

    public function testValidationFailUpdateCoupon()
    {
        $coupon = factory(Coupons::class)->create();

        $updateCouponData = [
            'discount' => "discount",
        ];

        $this->apiAs('PUT', route('api.coupons.update', $coupon->id), $updateCouponData)
            ->assertStatus(422);
    }

    public function testNotFoundUpdateCoupon()
    {
        $updateCouponData = [
            'code' => $this->faker->unique()->word,
            'discount' => $this->faker->numberBetween(0, 100),
        ];

        $this->apiAs('PUT', route('api.coupons.update', 1), $updateCouponData)
            ->assertStatus(404);
    }

    public function testSuccessDestroyCoupon()
    {
        $coupon = factory(Coupons::class)->create();

        $this->apiAs('DELETE', route('api.coupons.destroy', $coupon->id))
            ->assertStatus(200);

        $this->assertDatabaseCount($coupon->getTable(), 0);
    }

    public function testNotFoundDestroyCoupon()
    {
        $this->apiAs('DELETE', route('api.coupons.destroy', 1))
            ->assertStatus(404);
    }

    public function testSuccessShowCoupon()
    {
        $coupon = factory(Coupons::class)->create();
        $data = new CouponResource($coupon);
        $res = $this->apiAs('GET', route('api.coupons.show', $coupon->id))
            ->assertStatus(200)
            ->json();

        $this->assertEquals($data->toArray(request()), $res['data']);
    }

    public function testNotFoundShowCoupon()
    {
        $this->apiAs('GET', route('api.coupons.show', 1))
            ->assertStatus(404);
    }

    public function testSuccessGetAllCoupons()
    {
        factory(Coupons::class, 5)->create();

        $response = $this->apiAs('GET', route('api.coupons.index'))
            ->assertStatus(200)->json();

        $this->assertCount(5, $response['data']);
    }

    public function testSuccessGetEditOptions()
    {
        $response = $this->apiAs('GET', route('api.coupons.getOptions', 'edit'))
            ->assertStatus(200)
            ->json();

        $this->assertTrue(isset($response['statusOptions']));
    }

    public function testAssignProductsOnCoupon()
    {
        $coupon = factory(Coupons::class)->create();
        $products = factory(Products::class, 5)->create()->pluck('id')->toArray();
        $params = [
            'coupon_id' => $coupon->id,
            'products' => $products
        ];

        $response = $this->apiAs('POST', route('api.coupons.assignProducts'), $params)
            ->assertStatus(200)
            ->json();

        $this->assertCount(count($products), $response['products']);
    }

    public function testAssignAllProductsOnCoupon()
    {
        $coupon = factory(Coupons::class)->create();
        $products = factory(Products::class, 5)->create()->pluck('id')->toArray();
        $params = [
            'coupon_id' => $coupon->id,
            'assign_all' => true
        ];

        $response = $this->apiAs('POST', route('api.coupons.assignProducts'), $params)
            ->assertStatus(200)
            ->json();

        $this->assertCount(count($products), $response['products']);
    }

    public function testAssignVendorsOnCoupon()
    {
        $coupon = factory(Coupons::class)->create();
        $products = factory(Vendors::class, 5)->create()->pluck('id')->toArray();
        $params = [
            'coupon_id' => $coupon->id,
            'vendors' => $products
        ];

        $response = $this->apiAs('POST', route('api.coupons.assignVendors'), $params)
            ->assertStatus(200)
            ->json();

        $this->assertCount(count($products), $response['vendors']);
    }

    public function testAssignAllVendorsOnCoupon()
    {
        $coupon = factory(Coupons::class)->create();
        $vendors = factory(Vendors::class, 5)->create()->pluck('id')->toArray();
        $params = [
            'coupon_id' => $coupon->id,
            'assign_all' => true
        ];

        $response = $this->apiAs('POST', route('api.coupons.assignVendors'), $params)
            ->assertStatus(200)
            ->json();

        $this->assertCount(count($vendors), $response['vendors']);
    }
}
