<?php

namespace App\Events\Subscriptions;

use App\Models\Subscription;
use Illuminate\Queue\SerializesModels;

class Expired
{
    use SerializesModels;

    /**
     * @var Subscription
     */
    public $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }
}
