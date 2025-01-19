<?php

namespace App\Models;

use App\Classes\Enum\Order\EnrolledType;
use App\Classes\Enum\Order\PaymentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class OrdersProducts
 *
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property string $price
 * @property string $discount_price
 * @property string $discount
 * @property string $name
 * @property int $quantity
 * @property int $enrolled
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Products $product
 * @property Orders $order
 */
class OrdersProducts extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'order_product';

    protected $fillable = [
        'order_id',
        'product_id',
        'price',
        'tax',
        'discount_price',
        'discount',
        'name',
        'quantity',
        'enrolled',
    ];

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeSearch($query, $value)
    {
        return $query->where('name', 'like', '%' . $value . '%')
            ->orWhereHas('order', function ($query) use ($value) {
                return $query->whereHas('user', function ($query) use ($value) {
                    return $query->where('name', 'like', '%' . $value . '%');
                });
            })->orWhere('order_id', 'like', '%' . $value . '%');
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
     * @param Builder $query
     * @return Builder
     */
    public function scopeScheduledEnrollment($query, $value)
    {
        $enrollStatuses = [EnrolledType::NOT_ENROLLED];
        if (getSetting('display_deleted_scheduled_enrollments') == 1) {
            $enrollStatuses[] = EnrolledType::DELETED;
        }
        return $query->whereHas('product', function ($query) {
            return $query->where('enrol_start_date', '>' , Carbon::now());
        })->whereHas('order', function ($query) {
            return $query->where('payment_status', PaymentStatus::PAYMENT_STATUS_COMPLETED);
        })
        ->whereIn('enrolled', $enrollStatuses);
    }

    /**
     * @return string
     */
    public function enrollStatus()
    {
        $statuses = self::enrollStatuses();
        return array_key_exists($this->enrolled, $statuses) ? $statuses[$this->enrolled] : '';
    }

    /**
     * @return array
     */
    public static function enrollStatuses()
    {
        return [
            EnrolledType::NOT_ENROLLED => __('orders.products.pending'),
            EnrolledType::DELETED => __('orders.products.deleted'),
            EnrolledType::ENROLLED => __('orders.products.enrolled'),
        ];
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeSeats($query, $value)
    {
        return $query->whereHas('orderProductSeat');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeScheduledNotEnrollment($query, $value)
    {
        return $query->whereHas('product', function ($query) {
            return $query->where('enrol_start_date', '<' , Carbon::now());
        })->where('enrolled', $value);
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        return $this->product ? $this->product->getName() : null;
    }

    /**
     * @return string
     */
    public function getEnrollStatus()
    {
        return $this->enrolled;
    }

    /**
     * Get the App\Models\Products record associated with the OrdersProducts.
     */
    public function product()
    {
        return $this->hasOne(Products::class, 'id', 'product_id');
    }

    /**
     * Get the App\Models\OrdersProductSeats record associated with the OrdersProducts.
     */
    public function orderProductSeat()
    {
        return $this->hasOne(OrdersProductSeats::class, 'order_product_id', 'id');
    }

    /**
     * Get the App\Models\OrdersProductSeats record associated with the OrdersProducts.
     */
    public function orderProductSeatUsed()
    {
        return $this->hasOne(OrdersProductSeatsUsed::class, 'order_product_id', 'id');
    }

    /**
     * Get the App\Models\User record associated with the OrdersProducts.
     */
    public function order()
    {
        return $this->hasOne(Orders::class, 'id', 'order_id');
    }
}
