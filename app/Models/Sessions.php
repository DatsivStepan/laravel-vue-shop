<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Sessions
 *
 * @property int $id
 * @property int $product_id
 * @property int $parent_id
 * @property string $name
 * @property string $link
 * @property string $description
 * @property string $checkout_info
 * @property int $time_start
 * @property int $time_end
 * @property int $visible
 * @property int $seats
 * @property string $sequence
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Sessions extends Model
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public static $statusOptions = [
        self::STATUS_INACTIVE => 'Inactive',
        self::STATUS_ACTIVE => 'Active'
    ];

    /**
     * @var string $table
     */
    protected $table = 'sessions';

    protected $fillable = [
        'product_id',
        'parent_id',
        'name',
        'link',
        'description',
        'checkout_info',
        'time_start',
        'time_end',
        'visible',
        'seats',
        'sequence'
    ];

    /**
     * @return string.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string.
     */
    public function getLink()
    {
        return $this->name;
    }

    /**
     * Get the App\Models\Products record associated with the Sessions.
     */
    public function product()
    {
        return $this->hasOne(Products::class, 'id', 'product_id');
    }
}
