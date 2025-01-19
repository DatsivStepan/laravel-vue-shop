<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'description' => $this->description,
            'parent_id' => $this->parent_id,
            'visible' => $this->visible,
            'id_number' => $this->id_number,
            'sortorder' => $this->sortorder,
            'image_src' => $this->getImageUrl(),
            'max_product_per_user' => $this->max_product_per_user,
            'products_count' => $this->getProductsCount(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
