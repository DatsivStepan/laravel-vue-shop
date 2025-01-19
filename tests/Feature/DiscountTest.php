<?php

namespace Tests\Feature;

use App\Classes\Enum\DiscountType;
use App\Http\Resources\Admin\Discounts\DiscountResource;
use App\Models\Discount;
use App\Models\Products;
use App\Models\Vendors;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class DiscountTest extends TestCase
{
    use WithoutMiddleware;
    use WithFaker;

    protected $tenancy = true;
    protected $createUser = true;

    public function testSuccessCreateDiscount()
    {
        $discount = [
            'name' => $this->faker->word,
            'discount' => $this->faker->numberBetween(0, 100),
            'time_end' => Carbon::tomorrow()->format(__('langconfig.iso8601')),
            'time_start' => Carbon::yesterday()->format(__('langconfig.iso8601')),
            'type' => DiscountType::ALL,
        ];

        $this->apiAs('POST', route('api.discounts.store'), $discount)
            ->assertStatus(201)
            ->assertJsonFragment($discount);
    }


    public function testValidationFailCreateDiscount()
    {
        $this->apiAs('POST', route('api.discounts.store'), [])
            ->assertStatus(422);
    }

    public function testSuccessUpdateDiscount()
    {
        $discount = factory(Discount::class)->create();
        $updateDiscountData = [
            'name' => $this->faker->word,
            'discount' => $this->faker->numberBetween(0, 100),
        ];

        $this->apiAs('PUT', route('api.discounts.update', $discount->id), $updateDiscountData)
            ->assertStatus(200)
            ->assertJsonFragment($updateDiscountData);
    }

    public function testValidationFailUpdateDiscount()
    {
        $discount = factory(Discount::class)->create();

        $updateDiscountData = [
            'discount' => "discount",
        ];

        $this->apiAs('PUT', route('api.discounts.update', $discount->id), $updateDiscountData)
            ->assertStatus(422);
    }

    public function testNotFoundUpdateDiscount()
    {
        $updateDiscountData = [
            'discount' => $this->faker->numberBetween(0, 100),
        ];

        $this->apiAs('PUT', route('api.discounts.update', 1), $updateDiscountData)
            ->assertStatus(404);
    }

    public function testSuccessDestroyDiscount()
    {
        $discount = factory(Discount::class)->create();

        $this->apiAs('DELETE', route('api.discounts.destroy', $discount->id))
            ->assertStatus(200);

        $this->assertDatabaseCount($discount->getTable(), 0);
    }

    public function testNotFoundDestroyDiscount()
    {
        $this->apiAs('DELETE', route('api.discounts.destroy', 1))
            ->assertStatus(404);
    }

    public function testSuccessShowDiscount()
    {
        $discount = factory(Discount::class)->create();
        $data = new DiscountResource($discount);
        $this->apiAs('GET', route('api.discounts.show', $discount->id))
            ->assertStatus(200)
            ->assertJsonFragment($data->toArray(request()));
    }

    public function testNotFoundShowDiscount()
    {
        $this->apiAs('GET', route('api.discounts.show', 1))
            ->assertStatus(404);
    }

    public function testSuccessGetAllDiscounts()
    {
        factory(Discount::class, 5)->create();

        $response = $this->apiAs('GET', route('api.discounts.index'))
            ->assertStatus(200)->json();

        $this->assertCount(5, $response['data']);
    }

    public function testAssignProductsOnDiscount()
    {
        $discount = factory(Discount::class)->create();
        $products = factory(Products::class, 5)->create()->pluck('id')->toArray();
        $params = [
            'discount_id' => $discount->id,
            'products' => $products
        ];
        $response = $this->apiAs('POST', route('api.discounts.assignProducts'), $params)
            ->assertStatus(200)
            ->json();

        $this->assertCount(count($products), $response['products']);
    }

    public function testAssignAllProductsOnDiscount()
    {
        $discount = factory(Discount::class)->create();
        $products = factory(Products::class, 5)->create()->pluck('id')->toArray();
        $params = [
            'discount_id' => $discount->id,
            'assign_all' => true
        ];

        $response = $this->apiAs('POST', route('api.discounts.assignProducts'), $params)
            ->assertStatus(200)
            ->json();

        $this->assertCount(count($products), $response['products']);
    }

    public function testAssignVendorsOnDiscount()
    {
        $discount = factory(Discount::class)->create();
        $products = factory(Vendors::class, 5)->create()->pluck('id')->toArray();
        $params = [
            'discount_id' => $discount->id,
            'vendors' => $products
        ];

        $response = $this->apiAs('POST', route('api.discounts.assignVendors'), $params)
            ->assertStatus(200)
            ->json();

        $this->assertCount(count($products), $response['vendors']);
    }

    public function testAssignAllVendorsOnDiscount()
    {
        $discount = factory(Discount::class)->create();
        $vendors = factory(Vendors::class, 5)->create()->pluck('id')->toArray();
        $params = [
            'discount_id' => $discount->id,
            'assign_all' => true
        ];

        $response = $this->apiAs('POST', route('api.discounts.assignVendors'), $params)
            ->assertStatus(200)
            ->json();

        $this->assertCount(count($vendors), $response['vendors']);
    }
}
