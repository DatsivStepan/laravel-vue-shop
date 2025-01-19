<?php

namespace App\Http\Resources;

use App\Classes\Enum\Settings\TaxSettings;
use App\Facades\UserSettings;
use App\Services\Order\OrderProductService;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSubscribeResource extends JsonResource
{
    /**
     * @var OrderProductService
     */
    private $orderProductService;


    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->orderProductService = app(OrderProductService::class);
    }

    public function toArray($request)
    {
        $userCurrency = UserSettings::getCurrency($request->user());
        $defaultCurrency = getSetting('currency');

        $product = clone $this->resource;
        $tax = $this->orderProductService->calculateProductsTax($product, $request->user())[0]['tax'];
        $total = $this->price + $tax;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'image_src' => $this->getImageSrc(),
            'formatted_price' => currency($this->price, $defaultCurrency, $userCurrency),
            'currency' => $userCurrency,
            'tax' => [
                'label' => getSetting(TaxSettings::TAX_LABEL) ?: __('taxes.tax'),
                'value' => currency($tax, $defaultCurrency, $userCurrency),
            ],
            'total' => $total,
            'formatted_total' => currency($total, $defaultCurrency, $userCurrency),

            'recurring_period' => __("subscriptions.recurring_periods.$this->recurring_period"),
            'billing_cycles' => $this->billing_cycles,
            'user_id' => $request->user()->id ?? null,
        ];
    }

}
