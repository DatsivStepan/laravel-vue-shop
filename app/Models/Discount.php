<?php

namespace App\Models;

use App\Classes\Enum\DiscountType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * Class Discount
 *
 * @property int $id
 * @property string $name
 * @property float $discount
 * @property Carbon $time_start
 * @property Carbon $time_end
 * @property integer $used_per_user
 * @property integer $max_applied_products
 * @property bool $status
 * @property int $type
 * @property int $min_number
 * @property int $max_number
 * @property string $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Discount extends Model
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    protected $guarded = ['id'];

    public $sortable = [
        'name',
        'discount',
        'time_start',
        'time_end',
        'created_at'
    ];

    public $searchable = [
        'name'
    ];

    public static $typeOptions = [
        DiscountType::ALL => 'All Products',
        DiscountType::ANY => 'Any Products',
        DiscountType::CONDITION => 'Condition'
    ];

    /**
     * @param Builder $query
     * @param mixed $value
     * @return Builder
     */
    public function scopeSearch($query, $value)
    {
        return $query->where('name', 'like', '%' . $value . '%');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Products::class, 'discount_product', 'discount_id', 'product_id')
            ->withTimestamps();
    }

    public function vendors(): BelongsToMany
    {
        return $this->belongsToMany(Vendors::class, 'discount_vendor', 'discount_id', 'vendor_id')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Orders::class, 'discount_order', 'discount_id', 'order_id')
            ->withTimestamps();

    }

    public function appliedProducts(): int
    {
        return $this->orders()->withPivot('product_count')->sum('product_count');
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
            DiscountType::ALL => __('discounts.types.all'),
            DiscountType::ANY => __('discounts.types.any'),
            DiscountType::CONDITION => __('discounts.types.condition'),
        ];
    }
}
