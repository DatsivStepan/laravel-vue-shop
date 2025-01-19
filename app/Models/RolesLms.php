<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RolesLms
 *
 * @property int $id
 * @property int $role_id
 * @property int $lms_role_id
 * @property string $lms_role_name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class RolesLms extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'roles_lms';

    protected $fillable = [
        'role_id',
        'lms_role_id',
        'lms_role_name'
    ];

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeSearch($query, $value)
    {
        return $query->where('lms_role_name', 'like', '%' . $value . '%');
    }

    /**
     * Get the App\Models\Roles record associated with the RolesLms.
     */
    public function role()
    {
        return $this->hasOne(Roles::class, 'id', 'role_id');
    }
}
