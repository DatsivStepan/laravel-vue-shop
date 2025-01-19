<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorAssignResource extends JsonResource
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
            'vendorName' => $this->getVendorName(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
