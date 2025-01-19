<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Shipping
 *
 * @property int $id
 * @property int $order_id
 * @property string $name
 * @property string $email
 * @property string $address
 * @property string $city
 * @property string $state
 * @property string $zip_code
 * @property string $phone
 * @property int $note
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Shipping extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'shipping';

    protected $fillable = [
        'order_id',
        'name',
        'email',
        'address',
        'city',
        'state',
        'zip_code',
        'phone',
        'note'
    ];

    /**
     * @param Builder $query
     * @param mixed $value
     * @return Builder
     */
    public function scopeSearch($query, $value)
    {
        $query->where(function (Builder $query) use ($value) {
            $query->orWhere('order_id', 'like', '%' . $value . '%')
                ->orWhere('name', 'like', '%' . $value . '%')
                ->orWhere('address', 'like', '%' . $value . '%')
                ->orWhere('city', 'like', '%' . $value . '%')
                ->orWhere('state', 'like', '%' . $value . '%')
                ->orWhere('zip_code', 'like', '%' . $value . '%')
                ->orWhere('phone', 'like', '%' . $value . '%')
                ->orWhere('email', 'like', '%' . $value . '%');
        });
    }

    /**
     * @param Builder $query
     * @param mixed $vendors
     * @return Builder
     */
    public function scopeProductsVendors($query, ...$vendors)
    {
        return $query->whereHas('order', function ($query) use ($vendors) {
            return $query->productsVendors(...$vendors);
        });
    }

    /**
     * Get the App\Models\Orders record associated with the Shipping.
     */
    public function order()
    {
        return $this->hasOne(Orders::class, 'id', 'order_id');
    }
}
