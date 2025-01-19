<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'street_address' => $this->street_address,
            'apartment' => $this->apartment,
            'city' => $this->city,
            'country' => $this->country,
            'zip' => $this->zip,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
