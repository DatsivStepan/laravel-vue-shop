<?php

namespace App\Http\Resources\Admin\Taxes;

use App\Helpers\DateHelper;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Taxes\AdvancedTaxesField */
class AdvancedTaxesFieldResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'model_class' => $this->model_class,
            'key' => $this->key,
            'name' => $this->name,
            'created_at' => DateHelper::dateFormat($this->created_at),
        ];
    }
}
