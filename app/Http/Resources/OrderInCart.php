<?php

namespace App\Http\Resources;

use App\Classes\Enum\Order\BillingType;
use App\Classes\Enum\Settings\TaxSettings;
use App\Facades\UserSettings;
use App\Models\User;
use App\Services\Order\OrderProductService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderInCart extends JsonResource
{
    /**
     * @var OrderProductService
     */
    private $calculateProductDiscount;
    /**
     * @var string
     */
    private $billingType;

    public function __construct($resource, string $billingType = BillingType::REGULAR)
    {
        parent::__construct($resource);

        $this->calculateProductDiscount = app(OrderProductService::class);
        $this->billingType = $billingType;
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        if (!$this->resource) {
            return [];
        }

        $data = $this->calculate($request->user());

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'products' => $data['products'],
            'currency' => $data['userCurrency'],
            'note' => $this->note,
            'subtotal' => $data['subtotal'],
            'tax' => [
                'label' => getSetting(TaxSettings::TAX_LABEL) ?: __('taxes.tax'),
                'value' => currency($data['sumTaxes'], $data['defaultCurrency'], $data['userCurrency']),
            ],
            'formatted_refunded' => currency($this->refunded(), $data['defaultCurrency'], $data['userCurrency']),
            'tax_amount' => $data['sumTaxes'],
            'formatted_subtotal' => currency($data['subtotal'], $data['defaultCurrency'], $data['userCurrency']),
            'discount' => $data['discount'],
            'formatted_discount' => currency($data['discount'], $data['defaultCurrency'], $data['userCurrency']),
            'total' => $data['total'],
            'billing_type' => BillingType::REGULAR,
            'formatted_total' => currency($data['total'], $data['defaultCurrency'], $data['userCurrency']),
            'discounts' => $this->discounts()->pluck('name'),
            'enable_shipping' => $this->products()->where('enable_shipping', 1)->exists() ? true : false,
            'coupons' => $this->coupons()->select('coupons.id', 'code')->get(),
            'product_seats' => $this->productSeatsUsed()->select('order_product_seats_used.id', 'seat_key')->get(),
        ];
    }

    /**
     * @param User $user
     * @return array
     */
    private function calculate(User $user): array
    {
        $products = $this->calculateProductDiscount->calculateProductsPrice($this->resource)->toArray();

        $userCurrency = UserSettings::getCurrency($user);
        $defaultCurrency = getSetting('currency');

        foreach ($products as &$product) {
            $product['formatted_price'] = currency($product['price'], $defaultCurrency, $userCurrency);
            $product['formatted_discount_price'] = currency($product['discount_price'], $defaultCurrency,
                $userCurrency);
            $product['total'] = round(($product['discount_price'] + $product['tax']) * $product['quantity'], 2);
            $product['subtotal'] = round($product['price'] * $product['quantity'], 2);
            $product['sum_discount_price'] = round($product['discount_price'] * $product['quantity'], 2);
        }

        $sumDiscountPrice = round(array_sum(array_column($products, 'sum_discount_price')), 2);
        $sumTaxes = round(array_sum(array_column($products, 'tax')), 2);
        $total = round(array_sum(array_column($products, 'total')), 2);
        $total = $total > 0 ? $total : 0;

        $subtotal = round(array_sum(array_column($products, 'subtotal')), 2);

        $discount = round($subtotal - $sumDiscountPrice, 2);
        $discount = $discount > 0 ? $discount : 0;

        return [
            'subtotal' => $subtotal,
            'products' => $products,
            'userCurrency' => $userCurrency,
            'sumTaxes' => $sumTaxes,
            'defaultCurrency' => $defaultCurrency,
            'discount' => $discount,
            'total' => $total,
        ];
    }
}
