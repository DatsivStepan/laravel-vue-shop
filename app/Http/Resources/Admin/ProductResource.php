<?php

namespace App\Http\Resources\Admin;

use App\Facades\UserSettings;
use App\Http\Resources\Admin\Products\InstallmentResource;
use App\Models\Products;
use App\Services\Admin\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $userCurrency;

    /**
     * @var ProductService
     */
    private $productService;

    /**
     * ProductResource constructor.
     *
     * @param $resource
     * @param string|null $userCurrency
     */
    public function __construct($resource, string $userCurrency = null)
    {
        parent::__construct($resource);

        $this->productService = app(ProductService::class);

        if ($userCurrency) {
            $this->userCurrency = $userCurrency;
        }

        $this->currency = getSetting('currency');
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        if (is_null($this->currency)) {
            $this->userCurrency = UserSettings::getCurrency($request->user());
        }

        $discounts = $this->activeDiscountsAnyType;
        $discount = $this->productService->calculateProductDiscount($this->resource);
        $discountPrice = round($this->price - $discount, 2);
        $customFields = $this->getCustomFields();
        $userID = $request->user()->id;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'id_number' => $this->id_number,
            'price' => $this->price,
            'formatted_price' => currency($this->price, $this->currency, $this->userCurrency),
            'discount_formatted_price' => currency($discountPrice, $this->currency, $this->userCurrency),
            'discounts' => $discounts,
            'custom_fields' => $customFields,
            'visible' => $this->visible,
            'seats' => $this->seats,
            'featured' => $this->featured,
            'time_start' => $this->time_start ? date('Y-m-d H:i', $this->time_start) : 0,
            'time_end' => $this->time_end ? date('Y-m-d H:i', $this->time_end) : 0,
            'enable_sessions' => $this->enable_sessions,
            'featured_image_src' => $this->getFeaturedImageSrc(),
            'image_src' => $this->getImageSrc(),
            'video_src' => $this->getVideoSrc(),
            'description' => $this->description,
            'billing_cycles' => $this->billing_cycles,
            'recurring_period' => $this->recurring_period,
            'short_description' => $this->short_description,
            'checkout_info' => $this->checkout_info,
            'enable_shipping' => $this->enable_shipping,
            'enable_tax' => $this->enable_tax,
            'courses' => $this->courses(),
            'categories' => $this->getSelectedCategoriesData(),
            'instructors' => $this->getSelectedInstructorsIds(),
            // info for pay
            'in_cart' => $this->existsProductInCart($userID),
            'is_bought' => $this->isUserBoughtProduct($userID),
            'in_waitlist' => $this->existsProductInWaitlist($userID),
            'in_favorite' => $this->existsProductInFavorites($userID),
            'enable_seats' => $this->enable_seats,
            'available_seats' => $this->getAvailableSeats($userID),
            'enable_buy_ing_seats' => $this->enable_buy_ing_seats,
            'max_seats_per_user' => $this->max_seats_per_user,
            'rating' => $this->getRating(),
            //instalment
            'installment' => $this->getInstallment($request),
            'billing_type' => $this->billing_type,
            // info for pay
            'purchased_times' => $this->countSoldProducts(),
            'model' => Products::class,
            'theme_key' => $this->theme_key,
            'enrol_start' => $this->enrol_start,
            'enrol_on' => $this->enrol_on,
            'enrol_start_date' => $this->enrol_start_date ? $this->enrol_start_date : null,
            'enrol_end' => $this->enrol_end,
            'enrol_period' => $this->enrol_period,
            'enable_reviews' => $this->enable_reviews,
            'enable_reviews_approval' => $this->enable_reviews_approval,

            'subscription_expiration_action' => $this->subscription_expiration_action,
            'subscription_cancellation_action' => $this->subscription_cancellation_action,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * @param $request
     * @return array|null
     */
    private function getInstallment($request): ?array
    {
        return (new InstallmentResource($this->installment))->toArray($request);
    }
}
