<?php

namespace App\Repositories\Admin;

use App\Http\Resources\Admin\SettingResource;
use App\Http\Resources\Admin\Settings\SettingCurrencyResource;
use App\Http\Resources\Admin\Settings\SettingThemesResource;
use App\Models\Files;
use App\Models\Settings;
use App\Repositories\BaseRepository;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class SettingsRepository extends BaseRepository
{
    /**
     * @return Settings
     */
    protected function getModelClass(): string
    {
        return Settings::class;
    }

    /**
     * @param bool $byCategory
     * @return string
     */
    public function getSettings(bool $byCategory = false): array
    {
        $settings = $this->get();

        $res = [];
        if ($settings) {
            $sCategories = $this->getModelClass()::getCategories();
            foreach ($settings as $key => $setting) {
                if ($byCategory) {
                    if (array_key_exists($setting->category, $sCategories)) {
                        if ($setting->category == Settings::CATEGORY_GENERAL && $setting->key === 'currency') {
                            $res[$sCategories[$setting->category]][] = new SettingCurrencyResource($setting);
                        } else if ($setting->category == Settings::CATEGORY_GENERAL && $setting->key === 'theme') {
                            $res[$sCategories[$setting->category]][] = new SettingThemesResource($setting);
                        } else {
                            $res[$sCategories[$setting->category]][] = new SettingResource($setting);
                        }
                    }
                } else {
                    $res[] = new SettingResource($setting);
                }
            }
        }

        return $res;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getSetting(string $key)
    {
        $cacheKey = ($this->getModelClass())::CACHE_SETTINGS_KEY;
        if (Cache::has($cacheKey) && ($settings = json_decode(Cache::get($cacheKey)))) {
            if (property_exists($settings, $key)) {
                return $settings->{$key};
            }
        } else {
            $setting = ($this->getModelClass())::where('key', $key)->first();
            if (!$setting) {
                return null;
            }

            return $setting->getValue();
        }

        return null;
    }

    /**
     * @param string $key
     * @param int $category
     * @return Model
     */
    public function removeSetting(string $key, int $category)
    {
        return ($this->getModelClass())::query()
            ->where(['key' => $key, 'category' => $category])
            ->delete();
    }

    /**
     * @param array $settingCategories
     * @return bool
     */
    public function saveSettings(array $settingCategories = [])
    {
        if ($settingCategories) {
            try {
                foreach ($settingCategories as $settings) {
                    foreach ($settings as $setting) {
                        if ($setting['type'] == Settings::TYPE_IMAGE) {
                            (new FilesRepository)
                                ->saveImages($setting['id'], Files::INSTANCE_TYPE_SETTING, $setting['key'],
                                    $setting['value']);
                            $setting['value'] = '';
                        }
                        $this->updateByConditions(['id' => $setting['id']], $setting);
                    }
                }

                self::cacheSettings(false);

                Cache::forget('userSetting');

                return true;
            } catch (Exception $ex) {
                return false;
            }
        }

        return false;
    }

    /**
     * @param bool $cache
     * @return bool
     */
    public static function cacheSettings($hasCache = true)
    {
        $cacheKey = Settings::CACHE_SETTINGS_KEY;

        if ($hasCache && Cache::has($cacheKey)) {
            return true;
        }

        if ($settings = Settings::get()) {
            $res = [];
            foreach ($settings as $setting) {
                $res[$setting->key] = $setting->getValue();
            }

            return Cache::put($cacheKey, json_encode($res), Carbon::now()->addMinutes(60));
        }

        return true;
    }

    /**
     * @param int $type
     * @param int $category
     * @param string $name
     * @param string $key
     * @param string $description
     * @param mixed $defaultValue
     * @param array $params
     * @return bool
     */
    public function createSetting(
        int $type,
        int $category,
        string $name,
        string $key,
        string $description,
        $defaultValue,
        $params = [],
        $value = null
    ) {
        $modelClass = $this->getModelClass();
        $model = new $modelClass;
        $model->type = $type;
        $model->category = $category;
        $model->key = $key;
        $model->name = $name;
        $model->description = $description;
        $model->default_value = json_encode($defaultValue);
        $model->params = json_encode($params);
        $model->value = $value;

        return $model->save();
    }

    public function updateSetting(
        int $id,
        int $type,
        int $category,
        string $name,
        string $key,
        string $description,
        $defaultValue,
        $params = [],
        $value = null
    ) {
        $model = $this->getModelClass()::query()->find($id);
        $model->type = $type;
        $model->category = $category;
        $model->key = $key;
        $model->name = $name;
        $model->description = $description;
        $model->default_value = json_encode($defaultValue);
        $model->params = json_encode($params);

        if ($value) {
            $model->value = $value;
        }

        return $model->save();
    }

    /**
     * @param string $key
     * @param $value
     */
    public function updateByKey(string $key, $value)
    {
        $this->getModelClass()::query()->where('key', $key)
            ->update(['value' => $value]);
    }


}
