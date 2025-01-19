<?php

namespace Tests\Feature;

use App\Classes\Enum\Order\BillingType;
use App\Classes\Enum\Subscriptions\SubscriptionStatus;
use App\Models\Products;
use App\Models\Subscription;
use App\Models\User;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    protected $tenancy = true;
    protected $createUser = true;

    public function testSub()
    {
        $product = factory(Products::class)->create([
            'billing_cycles' => 3,
            'recurring_period' => 'Day',
            'billing_type' => BillingType::SUBSCRIPTION,
        ]);

        $this->app['config']->set('currency.driver', 'filesystem');

        $this->seed(\AddGeneralSettings::class);

        currency()->create([
            'name' => 'U.S. Dollar',
            'code' => 'USD',
            'symbol' => '$',
            'format' => '$1,0.00',
            'exchange_rate' => 1.00000000,
            'active' => 1,
        ]);

        $this->apiAs('GET', route('api.subscriptions.getSubscriptionData'), ['product_id' => $product->id])
            ->assertStatus(200)
            ->assertJsonFragment([
                "name" => $product->name,
                "price" => (string)$product->price,
                "formatted_price" => currency($product->price),
                "currency" => "USD",
                "tax" => [
                    "label" => __('taxes.tax'),
                    "value" => currency(0),
                ],
                "total" => $product->price,
                "formatted_total" => currency($product->price),
                "recurring_period" => __("subscriptions.recurring_periods.$product->recurring_period"),
                "billing_cycles" => "3",
            ]);

    }

    public function testGetAllSubscription()
    {
        factory(Subscription::class, 2)->create([
            'user_id' => $this->user->id,
        ]);

        $res = $this->apiAs('GET', route('api.subscriptions.index'))
            ->assertStatus(200)
            ->json();

        $this->assertCount(2, $res['data']['rowsItems']);
    }

    public function testGetMySubscription()
    {
        factory(Subscription::class, 2)->create([
            'user_id' => $this->user->id,
        ]);

        // Create subscription for another user
        factory(Subscription::class)->create([
            'user_id' => factory(User::class)->create()->id,
        ]);

        $res = $this->apiAs('GET', route('api.subscriptions.mySubscriptions'))
            ->assertStatus(200)
            ->json();

        $this->assertCount(2, $res['data']['rowsItems']);
    }

    public function testMakeCanceled()
    {
        $subscription = factory(Subscription::class)->create([
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals(SubscriptionStatus::ACTIVE, $subscription->status);

        $res = $this->apiAs('PATCH', route('api.subscriptions.cancel', $subscription->id))
            ->assertStatus(200)
            ->json();

        $this->assertEquals(__('subscriptions.statuses.canceled'), $res['data']['status']);

        $subscription->refresh();
        $this->assertEquals(SubscriptionStatus::CANCELED, $subscription->status);
    }

    public function testMakeExpired()
    {
        $subscription = factory(Subscription::class)->create([
            'user_id' => $this->user->id,
        ]);

        $this->assertEquals(SubscriptionStatus::ACTIVE, $subscription->status);

        $res = $this->apiAs('PATCH', route('api.subscriptions.expire', $subscription->id))
            ->assertStatus(200)
            ->json();

        $this->assertEquals(__('subscriptions.statuses.expired'), $res['data']['status']);

        $subscription->refresh();
        $this->assertEquals(SubscriptionStatus::EXPIRED, $subscription->status);
    }

    public function testShowDescription()
    {
        $subscription = factory(Subscription::class)->create([
            'user_id' => $this->user->id,
        ]);

        $this->apiAs('GET', route('api.subscriptions.show', $subscription->id))
            ->assertStatus(200);
    }
}
