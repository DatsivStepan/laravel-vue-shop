<?php

namespace App\Models;

use App\Classes\Enum\DiscountType;
use App\Classes\Enum\ModelStatus;
use App\Classes\Enum\Order\PaymentStatus;
use App\Classes\Enum\Product\EnrollEndType;
use App\Classes\Enum\Product\EnrollOnType;
use App\Classes\Enum\Product\EnrollStartType;
use App\Helpers\FunctionHelper;
use App\Models\Products\Installment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Reviews\Entities\Reviews;

/**
 * Class Products
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $short_description
 * @property string $checkout_info
 * @property string $price
 * @property string $id_number
 * @property int $visible
 * @property int $show_items
 * @property string $startnotifymessage
 * @property string $expirynotifymessage
 * @property int $sortorder
 * @property int $enable_seats
 * @property int $seats
 * @property int $deault
 * @property int $enable_buy_ing_seats
 * @property int $max_seats_per_user
 * @property int $enable_subscription
 * @property string $recurring_period
 * @property int $billing_cycles
 * @property int $enable_sessions
 * @property int $featured
 * @property int $time_start
 * @property int $time_end
 * @property int $enable_shipping
 * @property int $enable_tax
 * @property int $expiration
 * @property string $payment_code
 * @property string $payment_description
 * @property int $enable_reviews
 * @property int $enable_reviews_approval
 * @property int $show_instructors
 * @property int $enable_trial_access
 * @property int $product_completion
 * @property int $completion_min
 * @property int $completion_max
 * @property int $attendance
 * @property int $max_sessions_per_user
 * @property string $theme_key
 * @property int $enrol_start
 * @property int $enrol_on
 * @property string $enrol_start_date
 * @property int $enrol_end
 * @property int $enrol_period
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property mixed pivot
 */
class Products extends Model
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
    protected $table = 'products';

    protected $fillable = [
        'name',
        'price',
        'description',
        'visible',
        'time_start',
        'time_end',
        'featured',
        'id_number',
        'enable_sessions',
        'enable_seats',
        'seats',
        'short_description',
        'checkout_info',
        'enable_subscription',
        'billing_cycles',
        'recurring_period',
        'enable_buy_ing_seats',
        'max_seats_per_user',
        'enable_shipping',
        'enable_tax',
        'theme_key',
        'billing_type',
        'enrol_start',
        'enrol_on',
        'enrol_start_date',
        'enrol_end',
        'enrol_period',
        'enable_reviews',
        'enable_reviews_approval',
        'subscription_expiration_action',
        'subscription_cancellation_action',
    ];

    /**
     * @return array
     */
    public static function getSearchFields()
    {
        return [
            'name'
        ];
    }

    /**
     * @return array
     */
    public static function getDefaultSort()
    {
        return [
            'column' => 'sortorder',
            'direction' => 'desc'
        ];
    }

    /**
     * @param int $userId
     *
     * @return bool.
     */
    public function existsProductInCart(int $userId)
    {
        return $this->orders()
            ->where('payment_status', PaymentStatus::PAYMENT_STATUS_IN_CART)
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function isUserBoughtProduct(int $userId): bool
    {
        return $this->orders()
            ->where('payment_status', PaymentStatus::PAYMENT_STATUS_COMPLETED)
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * @param int $userId
     *
     * @return string.
     */
    public function existsProductInWaitlist(int $userId)
    {
        return $this->waitlist()
            ->active()
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * @param int $userId
     *
     * @return string.
     */
    public function existsProductInFavorites(int $userId)
    {
        return $this->favorites()
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * @return array.
     */
    public static function getEnrolStartTypes()
    {
        return [
            EnrollStartType::NOW => __('products.start_type.now'),
            EnrollStartType::TODAY => __('products.start_type.today'),
            EnrollStartType::COURSE_START => __('products.start_type.course_start'),
            EnrollStartType::SPECIFIC_DATE => __('products.start_type.specific_date'),
        ];
    }

    /**
     * @return array.
     */
    public static function getEnrolOnTypes()
    {
        return [
            EnrollOnType::CHECKOUT => __('products.on_type.checkout'),
            EnrollOnType::ENROLLMENT_START => __('products.on_type.enrollment_start'),
        ];
    }

    /**
     * @return array.
     */
    public static function getEnrolEndTypes()
    {
        return [
            EnrollEndType::BASED_OF_DURATION => __('products.end_type.based_of_duration'),
            EnrollEndType::COURSE_END => __('products.end_type.course_end')
        ];
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $value)
    {
        return $query->where('name', 'like', '%' . $value . '%');
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMyProducts($query, $userId)
    {
        return $query->whereHas('orders', function ($query) use ($userId) {
            return $query->where('user_id', $userId);
        });
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMyFavorites($query, $userId)
    {
        return $query->whereHas('favorites', function ($query) use ($userId) {
            return $query->where('user_id', $userId);
        });
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePrice($query, $from, $to)
    {
        return $query->whereBetween('price', [$from, $to]);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $vendors
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVendors($query, ...$vendors)
    {
        return $query->whereHas('vendors', function ($query) use ($vendors) {
            return $query->whereIn('vendor_id', $vendors);
        });
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInstructors($query, $instructors)
    {
        $instructors = explode(",", $instructors);
        return $query->whereHas('instructors', function ($query) use ($instructors) {
            return $query->whereIn('instructor_id', $instructors);
        });
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCategories($query, $categories)
    {
        $categories = explode(",", $categories);
        return $query->whereHas('categories', function ($query) use ($categories) {
            return $query->whereIn('category_id', $categories);
        });
    }

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
    public function getImageSrc()
    {
        return $this->imageFile ? $this->imageFile->getFileSrc() : getDefaultImageSrc();
    }

    /**
     * @return string.
     */
    public function getVideoSrc()
    {
        return $this->videoFile ? $this->videoFile->getFileSrc() : null;
    }

    /**
     * @return string.
     */
    public function getFeaturedImageSrc()
    {
        return $this->featuredImageFile ? $this->featuredImageFile->getFileSrc() : null;
    }

    /**
     * @return string
     */
    public function getRating()
    {
        $rate = $this->reviews()->avg('rate') ?: 0;
        return round($rate, 1);
    }

    /**
     * @param null|int $status
     * @return null|int
     */
    public function getWaitlist($status = null)
    {
        if (!$this->enable_seats || !getSetting('enable_waitlist')) {
            return null;
        }

        $aSeats = (int)$this->seats - (int)$this->countSoldProducts(true);
        if ($aSeats) {
            $waitLists = $this->waitlist()->active(null)->orderBy('created_at', 'ASC')->take($aSeats)->get();
            if ($status !== null) {
                return $waitLists->where(['sent' => ProductWaitlist::STATUS_NOT_SENT]);
            }
            return $waitLists;
        }

        return null;
    }

    /**
     * @param null|int $userId
     * @return null|int
     */
    public function getAvailableSeats($userId = null)
    {
        if (!$this->enable_seats) {
            return null;
        }

        $aSeats = $this->getAvailableSeatsWithoutWaitList();
        if ($userId && $aSeats && getSetting('enable_waitlist')) {
            $waitLists = $this->waitlist()->active($userId)->orderBy('created_at', 'ASC')->take($aSeats)->get();
            $nSeats = 0;
            if ($aSeats > $waitLists->count()) {
                $nSeats = $aSeats - $waitLists->count();
            }
            return $nSeats + ($waitLists->where('user_id', $userId)->count() ? 1 : 0);
        }

        return $aSeats;
    }

    /**
     * @return int
     */
    public function getAvailableSeatsWithoutWaitList(): int
    {
        if (!$this->enable_seats) {
            return 0;
        }

        return (int)$this->seats - (int)$this->countSoldProducts(true);
    }

    /**
     * @return array
     */
    public function getSelectedInstructorsIds()
    {
        $productId = $this->id;
        $teachers = User::whereHas('productsInstructors', function ($query) use ($productId) {
            return $query->where('product_id', '=', $productId);
        })->get();

        return $teachers ? FunctionHelper::modelsToVueOptions($teachers, 'id', 'name', true) : [];
    }

    /**
     * @return array
     */
    public function getCustomFields()
    {
        $resFields = [];
        if ($customFields = CustomFields::where(['instance_type' => 'product'])->get()) {
            foreach ($customFields as $customField) {
                $resFields[] = [
                    'id' => $customField->id,
                    'title' => $customField->title,
                    'value' => $customField->getValue($this->id)
                ];
            }
        }
        return $resFields;
    }

    /**
     * @return array
     */
    public function getSelectedCategoriesData()
    {
        return FunctionHelper::modelsToVueOptions($this->categories, 'id', 'name', true);
    }

    /**
     * Count sold product
     * @param bool $withPending
     * @param null|int $userId
     * @return int
     */
    public function countSoldProducts($withPending = false, $userId = null)
    {
        $options = [PaymentStatus::PAYMENT_STATUS_COMPLETED];
        if ($withPending) {
            $options = [PaymentStatus::PAYMENT_STATUS_COMPLETED, PaymentStatus::PAYMENT_STATUS_PENDING];
        }

        $query = $this->orders()
            ->whereIn('payment_status', $options);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->sum('order_product.quantity');
    }

    /**
     * Get the App\Models\Files record associated with the Products.
     */
    public function imageFile()
    {
        return $this->hasOne(Files::class, 'instance_id', 'id')
            ->where([
                'instance_key' => 'image',
                'instance_type' => Files::INSTANCE_TYPE_PRODUCT
            ]);
    }

    /**
     * Get the App\Models\Files record associated with the Products.
     */
    public function videoFile()
    {
        return $this->hasOne(Files::class, 'instance_id', 'id')
            ->where([
                'instance_key' => 'video',
                'instance_type' => Files::INSTANCE_TYPE_PRODUCT
            ]);
    }

    /**
     * Get the App\Models\Files record associated with the Products.
     */
    public function featuredImageFile()
    {
        return $this->hasOne(Files::class, 'instance_id', 'id')
            ->where([
                'instance_key' => 'featuredImage',
                'instance_type' => Files::INSTANCE_TYPE_PRODUCT
            ]);
    }

    /**
     * Get the App\Models\CustomFieldsValues record associated with the Products.
     */
    public function customFieldsValues()
    {
        return $this->hasMany(CustomFieldsValues::class, 'instance_id');
    }

    /**
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Categories::class, ProductCategories::class, 'product_id', 'category_id');
    }

    /**
     * Get the App\Models\ProductInstructors record associated with the Products.
     */
    public function instructors()
    {
        return $this->hasMany(ProductInstructors::class, 'instructor_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Courses::class, ProductCourses::class, 'product_id', 'course_id')->withPivot('id');
    }

    /**
     * @return BelongsToMany
     */
    public function vendors(): BelongsToMany
    {
        return $this->belongsToMany(Vendors::class, VendorsAssings::class, 'instance_id', 'vendor_id')
            ->withPivot('id')->wherePivot('instance_type', VendorsAssings::INSTANCE_TYPES_PRODUCT);
    }

    /**
     * Get the App\Models\ProductWaitlist record associated with the Products.
     */
    public function waitlist()
    {
        return $this->hasMany(ProductWaitlist::class, 'product_id', 'id');
    }

    /**
     * Get the App\Models\ProductFavorites record associated with the Products.
     */
    public function favorites()
    {
        return $this->hasOne(ProductFavorites::class, 'product_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Orders::class, 'order_product', 'product_id', 'order_id')
            ->withPivot([
                'price as order_product_price',
                'discount',
                'discount_price as order_discount_price',
                'quantity as quantity'
            ])
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupons::class, 'coupon_product', 'product_id', 'coupon_id')
            ->withTimestamps();
    }

    public function discounts(): BelongsToMany
    {
        return $this->belongsToMany(Discount::class, 'discount_product', 'product_id', 'discount_id')
            ->withTimestamps();
    }

    public function activeDiscounts(): BelongsToMany
    {
        return $this->belongsToMany(Discount::class, 'discount_product', 'product_id', 'discount_id')
            ->where('status', ModelStatus::ACTIVE)
            ->withTimestamps();
    }

    public function activeDiscountsAnyType(): BelongsToMany
    {
        return $this->belongsToMany(Discount::class, 'discount_product', 'product_id', 'discount_id')
            ->where('status', ModelStatus::ACTIVE)
            ->where('type', DiscountType::ANY)
            ->withTimestamps();
    }

    /**
     * Get the Reviews record associated with the Products.
     */
    public function reviews()
    {
        return $this->hasMany(Reviews::class, 'model_id', 'id')
            ->where([
                'model_type' => self::class,
            ]);
    }

    public function installment(): HasOne
    {
        return $this->hasOne(Installment::class, 'product_id');
    }
}
