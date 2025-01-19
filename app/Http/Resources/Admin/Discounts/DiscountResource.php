<?php

namespace App\Http\Resources\Admin\Discounts;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountResource extends JsonResource
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
            'name' => $this->name,
            'time_start' => $this->time_start ? Carbon::createFromTimeString($this->time_start)->format(__('langconfig.iso8601')) : '',
            'time_end' => $this->time_end ? Carbon::createFromTimeString($this->time_end)->format(__('langconfig.iso8601')) : '',
            'used_per_user' => $this->used_per_user,
            'max_applied_products' => $this->max_applied_products,
            'discount' => $this->discount,
            'status' => $this->status,
            'type' => $this->type,
            'min_number' => $this->min_number,
            'max_number' => $this->max_number,
            'description' => $this->description,
            'created_at' => $this->created_at ? Carbon::createFromFormat('Y-m-d H:i:s',
                $this->created_at)->format(__('langconfig.iso8601')) : '',
            'updated_at' => $this->updated_at ? Carbon::createFromFormat('Y-m-d H:i:s',
                $this->updated_at)->format(__('langconfig.iso8601')) : '',
        ];
    }
}
