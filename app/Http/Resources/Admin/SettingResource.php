<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
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
            'description' => $this->getDescription(),
            'default_value' => $this->getDefaultValue(),
            'value' => $this->getValue(),
            'params' => $this->getParams(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * @return array
     */
    private function getParams(): array
    {
        $params = json_decode($this->params, true);

        if (isset($params['options']) && is_array($params['options'])) {
            foreach ($params['options'] as $key => $option) {
                $params['options'][$key] = __($option);
            }
        }

        return $params ?? [];
    }
}
