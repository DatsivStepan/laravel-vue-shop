<?php

namespace Tests\Unit\Services;

use App\Models\Coupons;
use App\Models\Orders;
use App\Models\Products;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    /** @var OrderService $service */
    private $service;
    /** @var Orders $order */
    private $order;
    /** @var Coupons $coupon */
    private $coupon;
    /** @var Collection $products */
    private $products;

    protected $tenancy = true;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = app(OrderService::class);
        $this->user = factory(User::class)->create();
    }

    public function testSuccessAddProductsToCart()
    {
        $product = factory(Products::class)->create();
        factory(Orders::class)->create(['user_id' => $this->user->id]);

        $res = $this->service->addProductToCart($this->user->id, [
            'id' => $product->id,
            'seats' => 1,
            'delete_other' => false,
        ]);

        $this->assertTrue($res['status']);
    }

    public function testGetInCartOrderWithMethodGetOrderByUser()
    {
        $order = factory(Orders::class)->create(['user_id' => $this->user->id]);

        $receiveOrder = $this->service->getOrderByUser($this->user->id);

        $this->assertEquals($order->id, $receiveOrder->id);
    }

    public function testGetOrderByIDWithMethodGetOrderByUser()
    {
        $order = factory(Orders::class)->create(['user_id' => $this->user->id]);

        $receiveOrder = $this->service->getOrderByUser($this->user->id, $order->id);

        $this->assertEquals($order->id, $receiveOrder->id);
    }

    public function testGetOrderInCartWithoutOrCreate()
    {
        $order = factory(Orders::class)->create(['user_id' => $this->user->id]);

        $receiveOrder = $this->service->getOrderInCart($this->user->id);

        $this->assertEquals($order->id, $receiveOrder->id);
    }

    public function testGetOrderInCartWithOrCreate()
    {
        $receiveOrder = $this->service->getOrderInCart($this->user->id, true);

        $this->assertEquals(Orders::first()->id, $receiveOrder->id);
    }

    public function testSuccessAddCouponToOrder()
    {
        $this->prepareDateForTests();
        $this->assertEquals(1, $this->order->coupons()->count());
    }


    public function testChangePriceProductsInOrderAfterAddAnotherProductWithSameCoupon()
    {
        $this->prepareDateForTests();

        /** @var Products $newProduct */
        $newProduct = factory(Products::class)->create(['price' => 50]);
        $this->coupon->products()->syncWithoutDetaching([$newProduct->id]);
        $this->order->coupons()->syncWithoutDetaching([$this->coupon->id]);

        $this->service->addProductToCart($this->user->id, [
            'id' => $newProduct->id,
            'seats' => 1,
            'delete_other' => false,
        ]);

        $products = $this->order->products;
        $averageCouponDiscount = round($this->coupon->discount / $products->count(), 2);

        $averageProductsOrderDiscount = round($products->sum('discount') / $products->count(), 2);

        $this->assertEquals($averageCouponDiscount, $averageProductsOrderDiscount);
    }

    private function prepareDateForTests()
    {
        /** @var Coupons $coupon */
        $this->coupon = factory(Coupons::class)->create(['discount' => 10]);
        /** @var Orders $order */
        $this->order = factory(Orders::class)->create(['user_id' => $this->user->id]);
        /** @var Collection $products */
        $this->products = factory(Products::class, 2)->create(['price' => 100]);

        $this->order->coupons()->syncWithoutDetaching([$this->coupon->id => ['coupon_code' => $this->coupon->code]]);
        $this->coupon->products()->sync($this->products->pluck('id'));

        $productsData = [];
        foreach ($this->products as $product) {
            $discount = round($this->coupon->discount / count($this->products));
            $productsData[$product->id] = [
                'price' => $product->price,
                'quantity' => 1,
                'discount' => $discount,
                'name' => $product->name,
                'discount_price' => $product->price - $discount,
            ];
        }


        $this->order->products()->sync($productsData);
    }
}
