<?php

namespace App\Http\Resources\Admin\Products;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstallmentResource extends JsonResource
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
            'recurring_period' => $this->recurring_period ?? null,
            'billing_cycles' => $this->billing_cycles ?? null,
            'fee' => $this->fee ?? null,
            'fee_type' => $this->fee_type ?? null,
        ];
    }
}
