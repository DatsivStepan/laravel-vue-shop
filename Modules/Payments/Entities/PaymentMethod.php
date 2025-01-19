<?php

namespace Modules\Payments\Entities;

use Crypt;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Payments
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $settings
 * @property string $currency
 * @property int $status
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class PaymentMethod extends Model
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public static $statusOptions = [
        self::STATUS_INACTIVE => 'Inactive',
        self::STATUS_ACTIVE => 'Active',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'settings' => 'array',
    ];

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getSetting()
    {
        return $this->settings;
    }

    /**
     * @param $value
     * @return string
     */
    public function getPublicKeyAttribute($value): string
    {
        return Crypt::decryptString($value);
    }

    /**
     * @param $value
     */
    public function setPublicKeyAttribute($value)
    {
        $this->attributes['public_key'] = Crypt::encryptString($value);

    }

    /**
     * @param $value
     * @return string
     */
    public function getSecretKeyAttribute($value): string
    {
        return Crypt::decryptString($value);
    }

    /**
     * @param $value
     */
    public function setSecretKeyAttribute($value)
    {
        $this->attributes['secret_key'] = Crypt::encryptString($value);
    }
}
