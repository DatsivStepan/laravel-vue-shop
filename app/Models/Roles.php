<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Roles
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Roles extends Model
{
    const ROLE_NAME_ADMIN = 'Admin';
    const ROLE_NAME_MANAGER = 'Manager';
    const ROLE_NAME_USER = 'User';

    const DEFAULT_ROLE_NAME = 'User';

    protected $fillable = [
        'name',
        'guard_name'
    ];
}
