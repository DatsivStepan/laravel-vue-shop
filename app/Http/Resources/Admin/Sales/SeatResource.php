<?php

namespace App\Http\Resources\Admin\Sales;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeatResource extends JsonResource
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
            'order_product_id' => $this->order_product_id,
            'seat_key' => $this->seat_key,
            'status' => $this->status,
            'expiration' => $this->expiration,
            'used_count' => $this->usedCount(),
            'created_at' => $this->created_at,
        ];
    }
}
