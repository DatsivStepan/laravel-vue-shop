<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SessionResource extends JsonResource
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
            'product_id' => $this->product_id,
            'name' => $this->name,
            'link' => $this->link,
            'description' => $this->description,
            'checkout_info' => $this->checkout_info,
            'time_start' => $this->time_start,
            'time_end' => $this->time_end,
            'visible' => $this->visible,
            'seats' => $this->seats,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
