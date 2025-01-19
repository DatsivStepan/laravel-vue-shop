<?php

namespace App\Http\Resources;

use App\Facades\UserSettings;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'roles' => $this->getRolesInArray(),
            'permissions' => $this->getPermissionsInArray(),
            'settings' => $this->getUserSettings(),
            'userSettings' => UserSettings::all($this->resource),
            'countProductInCart' => $this->countProductInCart(),
            'waitlist_count' => $this->getWaitlistCount(),
            'favorites_count' => $this->getFavoritesCount(),
            'my_product_count' => $this->getMyProductCount(),
            'my_course_count' => $this->getMyCoursesCount(),
            'my_orders_count' => $this->getMyOrdersCount(),
        ];
    }
}
