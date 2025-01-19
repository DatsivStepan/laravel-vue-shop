<?php

namespace App\Http\Resources\Admin\Taxes;

use App\Helpers\DateHelper;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Taxes\AdvancedTaxesValue */
class AdvancedTaxesValueResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'tax' => $this->tax,
            'created_at' => DateHelper::dateFormat($this->created_at),
            'updated_at' => $this->updated_at,

            'field_id' => $this->field_id,
        ];
    }
}
