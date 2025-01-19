<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleLmsResource extends JsonResource
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
            'lms_role_id' => $this->lms_role_id,
            'lms_role_name' => $this->lms_role_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
