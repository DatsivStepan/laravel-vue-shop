<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Products
 *
 * @property int $id
 * @property string $code
 * @property int $timestart
 * @property int $timeend
 * @property int $expiration
 * @property int $usedperuser
 * @property int $usedcount
 * @property float $discount
 * @property int $status
 * @property int $type
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Coupons extends Model
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const TYPE_PERCENTS = 0;
    const TYPE_CURRENCY = 1;

    protected $guarded = ['id'];

    public $sortable = [
        'code',
        'discount',
        'timestart',
        'timeend',
        'created_at'
    ];

    public $searchable = [
        'code'
    ];

    /**
     * @param Builder $query
     * @param mixed $value
     * @return Builder
     */
    public function scopeSearch($query, $value)
    {
        return $query->where('code', 'like', '%' . $value . '%');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Products::class, 'coupon_product', 'coupon_id', 'product_id')->withTimestamps();
    }

    public function vendors(): BelongsToMany
    {
        return $this->belongsToMany(Vendors::class, 'coupon_vendor', 'coupon_id', 'vendor_id');
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Orders::class, 'coupon_product', 'coupon_id', 'product_id');
    }

    public static function getStatusOptions()
    {
        return [
            self::STATUS_INACTIVE => __('system.statuses.inactive'),
            self::STATUS_ACTIVE => __('system.statuses.active')
        ];
    }

    public static function getTypeOptions()
    {
        return [
            self::TYPE_PERCENTS => __('coupons.types.percents'),
            self::TYPE_CURRENCY => __('coupons.types.currency'),
        ];
    }
}
