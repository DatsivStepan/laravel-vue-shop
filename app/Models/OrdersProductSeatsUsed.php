<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class OrdersProductSeatsUsed
 *
 * @property int $id
 * @property int $order_id
 * @property string $order_product_seats_id
 * @property int $status
 * @property string $expiration
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property OrdersProductSeatsUsed $product
 */
class OrdersProductSeatsUsed extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'order_product_seats_used';

    protected $fillable = [
        'order_id',
        'order_product_seats_id',
        'status'
    ];

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeSearch($query, $value)
    {
        return $query->orWhereHas('order', function ($query) use ($value) {
                return $query->whereHas('user', function ($query) use ($value) {
                    return $query->where('name', 'like', '%' . $value . '%');
                });
            })->orWhereHas('ordersProductSeats', function ($query) use ($value) {
                return $query->whereHas('orderProduct', function ($query) use ($value) {
                    return $query->where('name', 'like', '%' . $value . '%');
                });
            });
    }

    /**
     * Get the App\Models\Orders record associated with the OrdersProductSeatsUsed.
     */
    public function order()
    {
        return $this->hasOne(Orders::class, 'id', 'order_id');
    }

    /**
     * Get the App\Models\OrdersProductSeats record associated with the OrdersProductSeatsUsed.
     */
    public function ordersProductSeats()
    {
        return $this->hasOne(OrdersProductSeats::class, 'id', 'order_product_seats_id');
    }
}
