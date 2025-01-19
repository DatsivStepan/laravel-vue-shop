<?php

namespace App\Listeners\Discount;

use App\Classes\Enum\ModelStatus;
use App\Facades\DiscountValidator;
use App\Models\Discount;
use Illuminate\Database\Eloquent\Builder;

class MakeInactive
{
    public function handle($event)
    {
        Discount::query()->where('status', ModelStatus::ACTIVE)
            ->whereHas('orders', function (Builder $query) use ($event) {
                $query->whereKey($event->order->id);
            })->each(function (Discount $discount) {
                if (!DiscountValidator::validateMaxApplied($discount)) {
                    $discount->update(['status' => ModelStatus::INACTIVE]);
                }
            });
    }
}
