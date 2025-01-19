<?php

namespace App\Http\Resources\Admin;

use App\Models\Coupons;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
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
            'code' => $this->code,
            'timestart' => $this->timestart ? Carbon::createFromTimestamp($this->timestart)->format(__('langconfig.iso8601')) : '',
            'timeend' => $this->timeend ? Carbon::createFromTimestamp($this->timeend)->format(__('langconfig.iso8601')) : '',
            'usedperuser' => $this->usedperuser,
            'usedcount' => $this->usedcount,
            'discount' => $this->discount,
            'formatted_discount' => $this->discountFormatting(),
            'status' => $this->status,
            'type' => $this->type,
            'created_at' => $this->created_at ? Carbon::createFromFormat('Y-m-d H:i:s',
                $this->created_at)->format(__('langconfig.iso8601')) : '',
            'updated_at' => $this->updated_at ? Carbon::createFromFormat('Y-m-d H:i:s',
                $this->updated_at)->format(__('langconfig.iso8601')) : '',
        ];
    }

    /**
     * @return string|null
     */
    private function discountFormatting(): ?string
    {
        $discount = null;

        if ($this->type == Coupons::TYPE_CURRENCY) {
            $discount = currency_format($this->discount);
        } else {
            if ($this->type == Coupons::TYPE_PERCENTS) {
                $discount = "$this->discount%";
            }
        }

        return $discount;
    }
}
