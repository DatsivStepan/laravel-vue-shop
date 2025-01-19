<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Settings
 *
 * $options = ['one' => 'one', 'two' => 'two'];
 * createSetting(\App\Models\Settings::TYPE_MULTISELECT, $name, $key, $description, $defaultValue, ['options' => $options]);
 *
 * @property int $id
 * @property int $type
 * @property string $key
 * @property int $category
 * @property string $name
 * @property string $description
 * @property string $default_value
 * @property string $value
 * @property string $params
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Settings extends Model
{
    const CACHE_SETTINGS_KEY = 'cacheSetting';

    const CATEGORY_GENERAL = 1;
    const CATEGORY_PRODUCT = 2;
    const CATEGORY_PRODUCT_CATEGORY = 2;
    const CATEGORY_COUPONS = 3;
    const CATEGORY_SALES = 4;
    const CATEGORY_INVOICE = 5;
    const CATEGORY_DISCOUNTS = 6;
    const CATEGORY_ENROLLMENTS = 7;
    const CATEGORY_VENDOR = 8;
    const CATEGORY_SESSIONS = 9;
    const CATEGORY_WAITLIST = 10;
    const CATEGORY_TAXES = 11;
    const CATEGORY_PRODUCT_CATALOG = 12;
    const CATEGORY_SUBSCRIPTIONS = 13;
    const CATEGORY_INSTALLMENTS = 14;
    const CATEGORY_REFUND = 15;

    const CATEGORY_HIDDEN = 100;

    const TYPE_INPUT_TEXT = 0;
    const TYPE_CHECKBOX = 1;
    const TYPE_SELECT = 2;
    const TYPE_MULTISELECT = 3;
    const TYPE_TIME_PERIOD = 4;
    const TYPE_INPUT_NUMBER = 5;
    const TYPE_IMAGE = 6;
    const TYPE_INPUT_TEXTAREA = 7;


    public static $timesPeriods = [
        "604800" => 'weeks',
        "86400" => 'days',
        "3600" => 'hours',
        "60" => 'minutes',
        "1" => 'seconds',
    ];

    /**
     * @var string $table
     */
    protected $table = 'settings';

    protected $fillable = [
        'type',
        'key',
        'default_value',
        'value',
        'params',
        'description'
    ];

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return json_decode($this->default_value);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return __($this->name);
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return __($this->description);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        if ($this->type == self::TYPE_IMAGE) {
            return $this->imageFile ? $this->imageFile->getFileSrc() : null;
        }

        return $this->value !== null ? $this->value : $this->getDefaultValue();
    }

    /**
     * @return array
     */
    public static function getCategories()
    {
        return [
            self::CATEGORY_GENERAL => __('settings.categories.general'),
            self::CATEGORY_PRODUCT => __('settings.categories.product'),
            self::CATEGORY_COUPONS => __('settings.categories.coupons'),
            self::CATEGORY_DISCOUNTS => __('settings.categories.discounts'),
            self::CATEGORY_SALES => __('settings.categories.sales'),
            self::CATEGORY_INVOICE => __('settings.categories.invoice'),
            self::CATEGORY_VENDOR => __('settings.categories.vendor'),
            self::CATEGORY_SESSIONS => __('settings.categories.sesisons'),
            self::CATEGORY_WAITLIST => __('settings.categories.waitlist'),
            self::CATEGORY_TAXES => __('settings.categories.taxes'),
            self::CATEGORY_PRODUCT_CATALOG => __('settings.categories.product_catalog'),
            self::CATEGORY_SUBSCRIPTIONS => __('settings.categories.subscriptions'),
            self::CATEGORY_INSTALLMENTS => __('settings.categories.installment'),
            self::CATEGORY_REFUND => __('settings.categories.refund'),
        ];
    }

    /**
     * Get the App\Models\Files record associated with the Products.
     */
    public function imageFile()
    {
        return $this->hasOne(Files::class, 'instance_id', 'id')
            ->where([
                'instance_key' => $this->key,
                'instance_type' => Files::INSTANCE_TYPE_SETTING
            ]);
    }
}
