<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VendorsAssings
 *
 * @property int $id
 * @property int $instance_type
 * @property int $instance_id
 * @property int $vendor_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class VendorsAssings extends Model
{
    const INSTANCE_TYPES_PRODUCT = 0;
    const INSTANCE_TYPES_CATEGORY = 1;
    const INSTANCE_TYPES_COUPON = 2;
    const INSTANCE_TYPES_DISCOUNT = 3;
    const INSTANCE_TYPES_PAYMENT = 4;

    /**
     * @var string $table
     */
    protected $table = 'vendors_assings';

    protected $fillable = [
        'instance_type',
        'instance_id',
        'vendor_id'
    ];

    /**
     * @return string
     */
    public function getVendorName()
    {
        return $this->vendor ? $this->vendor->name : '';
    }

    /**
     * Get the App\Models\Vendors record associated with the Vendors.
     */
    public function vendor()
    {
        return $this->hasOne(Vendors::class, 'id', 'vendor_id');
    }
}
