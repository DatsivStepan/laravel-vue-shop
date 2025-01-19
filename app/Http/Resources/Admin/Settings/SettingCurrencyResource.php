<?php

namespace App\Http\Resources\Admin\Settings;

use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingCurrencyResource extends JsonResource
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
            'type' => $this->type,
            'key' => $this->key,
            'name' => $this->getName(),
            'description' => $this->description,
            'default_value' => $this->getDefaultValue(),
            'value' => $this->getValue(),
            'params' => ['options' => Currency::query()->where('active', 1)->pluck('name', 'code')->toArray()],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
