<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * Class Permissions
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Permissions extends SpatiePermission
{
    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeSearch($query, $value)
    {
        return $query->where(function ($query) use ($value) {
            $query->orWhere('name', 'like', '%' . $value . '%');
        });
    }
}
