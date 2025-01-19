<?php

namespace App\Console\Commands;

use App\Classes\Enum\Subscriptions\SubscriptionStatus;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Payments\Classes\PaymentProviderFactory;

class CheckSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscriptions check';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info("Start check subscriptions\n");
        Subscription::query()->where('status', SubscriptionStatus::ACTIVE)
            ->each(function (Subscription $subscription) {
                $paymentMethod = PaymentProviderFactory::create($subscription->order->paymentMethod);

                $paymentMethod->checkSubscription($subscription->order->external_id);
                $subscription->refresh();
                Log::info("Subscription ID $subscription->id has status $subscription->status");
            });
        Log::info("End check subscriptions\n");
    }
}
