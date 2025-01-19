<?php

namespace App\Models;

use App\Classes\Enum\Order\BillingType;
use App\Classes\Enum\Order\PaymentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Modules\Payments\Entities\PaymentMethod;

/**
 * Class Orders
 *
 * @property int $id
 * @property int $user_id
 * @property float $subtotal
 * @property float $amount
 * @property float $discount
 * @property int $payment_status
 * @property int $payment_type
 * @property int $payment_id
 * @property int $payment_data
 * @property string $currency
 * @property string $note
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property User $user
 * @property PaymentMethod $paymentMethod
 */
class Orders extends Model
{
    use SoftDeletes;

    const PAYMENT_TYPE_INVOICE = 0;

    /**
     * @var string $table
     */
    protected $table = 'orders';

    protected $guarded = ['id'];

    /**
     * @return string
     */
    public function getCustomerName()
    {
        return $this->customer ? $this->customer->getName() : '';
    }

    /**
     * @return true
     */
    public function getStatus()
    {
        $statuses = self::getStatuses();

        return array_key_exists($this->payment_status, $statuses) ? $statuses[$this->payment_status] : '';
    }

    /**
     * @return true
     */
    public function completed()
    {
        $this->payment_status = PaymentStatus::PAYMENT_STATUS_COMPLETED;

        return $this->save();
    }

    /**
     * @return int
     */
    public function getProductsCount()
    {
        return $this->products()->sum('quantity');
    }

    /**
     * @return BelongsTo
     */
    public function paymentData(): BelongsTo
    {
        return $this->belongsTo(PaymentData::class, 'payment_data_id');
    }

    /**
     * Get the App\Models\User record associated with the Orders.
     */
    public function customer()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @param string $paymentType
     * @return bool
     */
    public function checkPaymentType($paymentType)
    {
        return $this->payment_type == $paymentType;
    }

    /**
     * @return bool
     */
    public function isPending()
    {
        return $this->payment_status == PaymentStatus::PAYMENT_STATUS_PENDING;
    }

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return [
            PaymentStatus::PAYMENT_STATUS_IN_CART => __('orders.status.in_cart'),
            PaymentStatus::PAYMENT_STATUS_PENDING => __('orders.status.pending'),
            PaymentStatus::PAYMENT_STATUS_COMPLETED => __('orders.status.completed'),
            PaymentStatus::PAYMENT_STATUS_REFUND => __('orders.status.refund'),
            PaymentStatus::PAYMENT_STATUS_REFUNDED_PARTIAL => __('orders.status.refunded_partial'),
            PaymentStatus::PAYMENT_STATUS_REJECTED => __('orders.status.rejected'),
        ];
    }

    /**
     * @return array
     */
    public static function getBillingTypes()
    {
        $result = [];

        if (getSetting('enable_subscription')) {
            $result[BillingType::SUBSCRIPTION] = __('products.billing_type.subscription');
        }
        if (getSetting('enable_installment')) {
            $result[BillingType::INSTALLMENT] = __('products.billing_type.installment');
        }

        $result[BillingType::REGULAR] = __('products.billing_type.regular');

        return $result;
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeFilterByUser($query, $userId)
    {
        $query->where('user_id', $userId);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeSearch($query, $value)
    {
        return $query->where(function ($query) use ($value) {
            $query->orWhere('note', 'like', '%' . $value . '%');
            $query->orWhere('id', 'like', '%' . $value . '%');
            $query->orWhereHas('user', function ($query) use ($value) {
                return $query->where('name', 'like', '%' . $value . '%');
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
     * @param Builder $query
     * @param mixed $vendors
     * @return Builder
     */
    public function scopeProductsVendors($query, ...$vendors)
    {
        return $query->whereHas('products', function ($query) use ($vendors) {
            return $query->vendors(...$vendors);
        });
    }

    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Products::class, 'order_product', 'order_id', 'product_id')
            ->withPivot([
                'price as order_product_price',
                'discount as discount',
                'discount_price as order_discount_price',
                'quantity as quantity',
                'tax as tax',
            ])
            ->withTimestamps();
    }

    /**
     * @return HasMany
     */
    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrdersProducts::class, 'order_id');
    }

    /**
     * @return BelongsToMany
     */
    public function productSeatsUsed(): BelongsToMany
    {
        return $this->belongsToMany(OrdersProductSeats::class, OrdersProductSeatsUsed::class, 'order_id', 'order_product_seats_id')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupons::class, 'coupon_order', 'order_id', 'coupon_id')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function discounts(): BelongsToMany
    {
        return $this->belongsToMany(Discount::class, 'discount_order', 'order_id', 'discount_id')
            ->withTimestamps();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function calculateSubtotal()
    {
        return DB::table('order_product')
            ->where('order_id', $this->id)
            ->sum('price');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * @return HasMany
     */
    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class, 'order_id');
    }

    /**
     * @return int
     */
    public function refunded(): int
    {
        return $this->refunds()->sum('amount');
    }
}
