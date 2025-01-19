<?php

namespace App\Http\Resources\Admin\Subscriptions;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'customer' => $this->user->name,
            'product' => $this->product_name,
            'subscription_date' => $this->created_at ? Carbon::createFromTimeString($this->created_at)->format(__('langconfig.iso8601')) : '',
            'price' => $this->price,
            'recurring_period' => __('subscriptions.recurring_periods.' . $this->recurring_period),
            'billing_cycles' => $this->cycles,
            'status' => __('subscriptions.statuses.' . $this->status),
        ];
    }
}
