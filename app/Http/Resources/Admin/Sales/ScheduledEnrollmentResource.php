<?php

namespace App\Http\Resources\Admin\Sales;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduledEnrollmentResource extends JsonResource
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
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'discount' => $this->discount,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'enrolled' => $this->enrolled,
            'created_at' => Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->format(__('langconfig.iso8601')),
        ];
    }
}
