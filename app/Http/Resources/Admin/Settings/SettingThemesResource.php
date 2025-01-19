<?php

namespace App\Http\Resources\Admin\Settings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\ThemeService;

class SettingThemesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $themes = ThemeService::getThemes();
        return [
            'id' => $this->id,
            'type' => $this->type,
            'key' => $this->key,
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'default_value' => $this->getDefaultValue(),
            'value' => $this->getValue(),
            'params' => ['options' => $themes],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
