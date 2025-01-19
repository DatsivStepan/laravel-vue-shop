<?php

namespace App\Facades;

use App\Models\User;
use App\Services\Admin\User\UserSettingsService;
use Illuminate\Support\Facades\Facade;

/**
 * Class UserSetting
 * @method static UserSettingsService getCurrency(User $user)
 * @method static UserSettingsService all(User $user)
 *
 * @package App\Services\Discount
 */
class UserSettings extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'UserSettings';
    }
}
