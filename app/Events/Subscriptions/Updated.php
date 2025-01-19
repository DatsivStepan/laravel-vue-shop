<?php

namespace App\Events\Subscriptions;

use App\Models\Subscription;
use Illuminate\Queue\SerializesModels;

class Updated
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
