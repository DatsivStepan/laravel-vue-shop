<?php

use App\Repositories\Admin\DebugLogRepository;
use App\Repositories\Admin\SettingsRepository;
use App\Tenancy\Tenant;
use Illuminate\Support\Facades\Auth;

/**
 * @param int $type
 * @param int $category
 * @param string $name
 * @param string $key
 * @param string $description
 * @param mixed $defaultValue
 * @param array $params
 *
 * @return bool
 */
function createSetting($type, $category, $name, $key, $description, $defaultValue, $params = [], $value = null)
{
    return (new SettingsRepository())->createSetting($type, $category, $name, $key, $description, $defaultValue,
        $params, $value);
}

function updateSetting($id, $type, $category, $name, $key, $description, $defaultValue, $params = [], $value = null)
{
    return (new SettingsRepository())->updateSetting($id, $type, $category, $name, $key, $description, $defaultValue,
        $params, $value);
}

/**
 * @param string $key
 * @param int $category
 *
 * @return bool
 */
function removeSetting($key, $category)
{
    return (new SettingsRepository())->removeSetting($key, $category);
}

/**
 * @param string $key
 *
 * @return mixed
 */
function getSetting($key)
{
    return (new SettingsRepository())->getSetting($key);
}

/**
 * @param string $table
 * @return int
 */
function getDefaultPaginateCount($table = '')
{
    $rPerPage = request()->get('perPage');
    if ($table && ($user = auth()->user())) {
        $settingKey = $table . '_per_page';
        if ($rPerPage) {
            $user->settings()->set($settingKey, $rPerPage);
        } else {
            $rPerPage = $user->settings()->get($settingKey);
        }
    }

    return $rPerPage ? : 10;
}

/**
 * @param float $price
 * @return string
 */
function formatPrices($price)
{
    return number_format($price, 2);
}

/**
 * @return array
 */
function getDefaultSelectOptions()
{
    return [
        0 => 'No',
        1 => 'Yes'
    ];
}

/**
 * @return array
 */
function getDatesPeriods()
{
    return [
        'DAY' => 'Day',
        'WEEK' => 'Week',
        'MONTH' => 'Month',
        'YEAR' => 'Year',
    ];
}

/**
 * @param array $roles
 * @return bool
 */
function userHasAnyRole($roles)
{
    $user = Auth::user();

    return $user && $user->hasAnyRole($roles) ? true : false;
}

/**
 * @param int $type
 * @param string $log
 * @param bool $die
 */
function debugLogCreate($type, $log = '', $die = true)
{
    if (getSetting('enable_debugging') && $die) {
        die ('Error with: ' . $log);
    }

    (new DebugLogRepository())->create([
        'type' => $type,
        'log' => $log
    ]);

}

/**
 * @param int $connectionId
 * @return bool
 */
function setTenant($connectionId)
{
    if ($tenant = Tenant::find($connectionId)) {
        tenancy()->initialize($tenant);

        return true;
    }

    return false;
}

/**
 * alias of env('APP_KEY'))
 *
 * @return string
 */
function getAppKey(): string
{
    return env('APP_KEY');
}
