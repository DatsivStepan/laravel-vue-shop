<?php

namespace App\Http\Resources\Admin\Currency;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'symbol' => $this->symbol,
            'format' => $this->format,
            'exchange_rate' => $this->exchange_rate,
            'active' => $this->active,
            'is_default' => $this->code === getSetting('currency'),
            'created_at' => $this->created_at ? Carbon::createFromFormat('Y-m-d H:i:s',
                $this->created_at)->format(__('langconfig.iso8601')) : '',
            'updated_at' => $this->updated_at ? Carbon::createFromFormat('Y-m-d H:i:s',
                $this->updated_at)->format(__('langconfig.iso8601')) : '',
        ];
    }
}
