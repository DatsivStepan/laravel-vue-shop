<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class OrdersProductSeats
 *
 * @property int $id
 * @property int $order_product_id
 * @property string $seat_key
 * @property int $status
 * @property string $expiration
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property OrdersProducts $product
 */
class OrdersProductSeats extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'order_product_seats';

    protected $fillable = [
        'order_product_id',
        'seat_key',
        'status',
        'expiration',
    ];

    /**
     * @return bool
     */
    public function checkAvailability()
    {
        if (!$this->orderProduct) {
            return false;
        }

        return $this->orderProduct->quantity > (int)$this->usedCount() ? true : false;
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeSearch($query, $value)
    {
        return $query->where('seat_key', 'like', '%' . $value . '%')
            ->orWhereHas('orderProduct', function ($query) use ($value) {
                return $query->where('name', 'like', '%' . $value . '%')
                    ->orWhere('order_id', 'like', '%' . $value . '%')
                    ->orWhereHas('order', function ($query) use ($value) {
                        return $query->whereHas('user', function ($query) use ($value) {
                            return $query->where('name', 'like', '%' . $value . '%');
                        });
                    });
        });
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopePaidOnBetween($query, $fromDate, $toDate)
    {
        return $query->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate);
    }

    /**
     * @return int
     */
    public function usedCount()
    {
        return $this->seatUsed()->count();
    }

    /**
     * Get the App\Models\Products record associated with the OrdersProductSeats.
     */
    public function orderProduct()
    {
        return $this->hasOne(OrdersProducts::class, 'id', 'order_product_id');
    }

    /**
     * Get the App\Models\OrdersProductSeatsUsed record associated with the OrdersProductSeats.
     */
    public function seatUsed()
    {
        return $this->hasOne(OrdersProductSeatsUsed::class, 'order_product_seats_id', 'id');
    }
}
