<?php

namespace App\Models;

use App\Classes\Enum\Order\PaymentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class ProductWaitlist
 *
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property int $sent
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Products $product
 * @property User $user
 */
class ProductWaitlist extends Model
{
    const STATUS_NOT_SENT = 0;
    const STATUS_SENT = 1;

    /**
     * @var string $table
     */
    protected $table = 'product_waitlist';

    protected $fillable = [
        'user_id',
        'product_id',
        'sent'
    ];

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->user ? $this->user->getName() : null;
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeSearch($query, $value)
    {
        return $query->whereHas('product', function ($query) use ($value) {
            $query->where('name', 'like', '%' . $value . '%');
        });
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        return $this->product ? $this->product->getName() : null;
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeNoSent(Builder $query): Builder
    {
        return $query->where('sent', self::STATUS_NOT_SENT);
    }

    /**
     * @param null|int $userId
     * @return query
     */
    public function scopeActive($query, $userId = null)
    {
        $duration = getSetting('waitlist_duration');

        return $query->where(function (Builder $query) use ($duration, $userId) {
            $query->orWhere(['sent' => self::STATUS_NOT_SENT])
                ->orWhere(function (Builder $query) use ($duration, $userId) {
                    $query->where(['sent' => self::STATUS_SENT])
                        ->where(DB::raw('UNIX_TIMESTAMP(`updated_at`)'), '>', strtotime('-' . $duration . ' seconds'));
                    if ($userId) {
                        $query->whereDoesntHave('product', function ($query) use ($userId) {
                            $query->whereHas('orders', function ($query) use ($userId) {
                                $query->where('payment_status', PaymentStatus::PAYMENT_STATUS_COMPLETED)
                                    ->where('user_id', $userId);
                            });
                        });
                    }
                });
        });
    }


    /**
     * @param Builder $query
     * @param null|int $userId
     * @return Builder
     */
    public function scopeInactive(Builder $query, $userId = null): Builder
    {
        $duration = getSetting('waitlist_duration');

        return $query->where(function (Builder $query) use ($duration, $userId) {
            $query->orWhere(function (Builder $query) use ($duration, $userId) {
                $query->where(['sent' => self::STATUS_SENT])
                    ->where(DB::raw('UNIX_TIMESTAMP(`updated_at`)'), '<', strtotime('-' . $duration . ' seconds'));
                if ($userId) {
                    $query->whereDoesntHave('product', function ($query) use ($userId) {
                        $query->whereHas('orders', function ($query) use ($userId) {
                            $query->where('payment_status', PaymentStatus::PAYMENT_STATUS_COMPLETED)
                                ->where('user_id', $userId);
                        });
                    });
                }
            });
        });
    }

    /**
     * @return string
     */
    public function getSentStatus()
    {
        return $this->sent ? __('waitlist.sent') : null;
    }

    /**
     * Get the App\Models\Products record associated with the ProductWaitlist.
     */
    public function product()
    {
        return $this->hasOne(Products::class, 'id', 'product_id');
    }

    /**
     * Get the App\Models\User record associated with the ProductWaitlist.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @return int
     */
    public function getSeatNumber(): int
    {
        return ProductWaitlist::query()->where('product_id', $this->product->id)
                ->where('created_at', '<', $this->created_at)->count() + 1;
    }
}
