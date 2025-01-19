<?php

namespace App\Services\Admin\User;

use App\Classes\Enum\User\UserSettingKey;
use App\Models\Currency;
use App\Models\User;
use Glorand\Model\Settings\Exceptions\ModelSettingsException;

class UserSettingsService
{
    /**
     * @param User $user
     * @param string $code
     *
     * @throws ModelSettingsException
     */
    public function selectCurrency(User $user, string $code): array
    {
        $this->set($user, UserSettingKey::CURRENCY, $code);

        return $this->all($user);
    }

    /**
     * @param User $user
     *
     * @return string
     * @throws ModelSettingsException
     */
    public function getCurrency(User $user): string
    {
        return $this->get($user, UserSettingKey::CURRENCY) ?? getSetting(UserSettingKey::CURRENCY);
    }

    /**
     * @param User $user
     * @param string $key
     * @param mixed $value
     *
     * @throws ModelSettingsException
     */
    public function set(User $user, string $key, $value)
    {
        $user->settings()->set($key, $value);
    }

    /**
     * @param User $user
     * @param string $key
     *
     * @return string
     * @throws ModelSettingsException
     */
    public function get(User $user, string $key)
    {
        return $user->settings()->get($key);
    }

    /**
     * @param User $user
     *
     * @return array
     * @throws ModelSettingsException
     *
     */
    public function all(User $user): array
    {
        $settings = $user->settings()->all();

        $settings['currency'] = $settings['currency'] ?? $this->getCurrency($user);

        $currency = Currency::query()->where('code', $settings['currency'])->first();
        $settings['currency_symbol'] = $currency ? $currency->symbol : null;

        return $settings;
    }
}
