<?php

namespace App\Http\Resources;

use App\Classes\Enum\Installments\FeeType;
use App\Classes\Enum\Order\BillingType;
use App\Classes\Enum\Settings\TaxSettings;
use App\Facades\UserSettings;
use App\Models\Products;
use App\Models\User;
use App\Services\Order\OrderProductService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstallmentOrderInCart extends JsonResource
{
    /**
     * @var OrderProductService
     */
    private $calculateProductDiscount;

    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->calculateProductDiscount = app(OrderProductService::class);

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

        $data = $this->calculate($this->user);

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'products' => $data['products'],
            'currency' => $data['userCurrency'],
            'note' => $this->note,
            'subtotal' => $data['subtotal'],
            'tax' => [
                'label' => getSetting(TaxSettings::TAX_LABEL) ?: __('taxes.tax'),
                'value' => $data['formatted_tax'],
            ],
            'tax_amount' => $data['tax'],
            'billing_type' => BillingType::INSTALLMENT,
            'formatted_subtotal' => $data['formatted_subtotal'],
            'total' => $data['total'],
            'formatted_total' => $data['formatted_total'],
            'recurring_period' => __("subscriptions.recurring_periods." . $data['recurring_period']),
            'billing_cycles' => $data['billing_cycles'],
            'installment_price' => $data['installment_price'],
            'formatted_installment_price' => $data['formatted_installment_price'],
            'fee' => $data['fee'],
            'formatted_fee' => $data['formatted_fee'],
            'enable_shipping' => $this->products()->where('enable_shipping', 1)->exists() ? true : false
        ];
    }

    /**
     * @param User $user
     * @return array
     */
    private function calculate(User $user): array
    {
        /** @var Products $product */
        $product = $this->calculateProductDiscount->calculateProductsTax($this->resource->products, $user)->first();

        $userCurrency = UserSettings::getCurrency($user);
        $defaultCurrency = getSetting('currency');
        $installment = $product->installment;

        $product['formatted_price'] = currency($product->price, $defaultCurrency, $userCurrency);
        if ($installment->fee_type == FeeType::PERCENT) {
            $fee = round(($product->price * $installment->fee / 100), 2);
        } else {
            $fee = $installment->fee;
        }


        $installmentPrice = round(($product->price / $installment->billing_cycles), 2);
        $total = round(($installmentPrice + $product->tax + $fee), 2);
        $total = $total > 0 ? $total : 0;

        return [
            'subtotal' => $product->price,
            'formatted_subtotal' => currency($product->price, $defaultCurrency, $userCurrency),
            'products' => [$product],
            'userCurrency' => $userCurrency,
            'tax' => $product->tax,
            'formatted_tax' => currency($product->tax, $defaultCurrency, $userCurrency),
            'defaultCurrency' => $defaultCurrency,
            'total' => $total,
            'formatted_total' => currency($total, $defaultCurrency, $userCurrency),
            'fee' => $fee,
            'formatted_fee' => currency($fee, $defaultCurrency, $userCurrency),
            'installment_price' => $installmentPrice,
            'formatted_installment_price' => currency($installmentPrice, $defaultCurrency, $userCurrency),
            'recurring_period' => $installment->recurring_period,
            'billing_cycles' => $installment->billing_cycles,
        ];
    }
}
