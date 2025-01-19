<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesResource extends JsonResource
{
    /**
     * @var string|null
     */
    private $reportCurrency;

    public function __construct($resource, $reportCurrency = null)
    {
        parent::__construct($resource);
        $this->reportCurrency = $reportCurrency;
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($reques)
    {
        $currency = $this->reportCurrency ?? $this->currency;
        $refunded = $this->refunded();

        return [
            'id' => $this->id,
            'status' => $this->getStatus(),
            'amount' => $this->amount,
            'formatted_amount' => currency($this->amount, $this->base_currency, $currency),
            'formatted_tax' => currency($this->tax, $this->base_currency, $currency),
            'formatted_refunded' => currency($refunded, $this->base_currency, $currency),
            'refunded' => $refunded,
            'discount' => $this->discount,
            'formatted_discount' => currency($this->discount, $this->base_currency, $currency),
            'subtotal' => $this->subtotal,
            'formatted_subtotal' => currency($this->subtotal, $this->base_currency, $currency),
            'billing_type' => $this->billing_type,
            'payment_type' => $this->payment_type,
            'note' => $this->note,
            'customer' => $this->getCustomerName(),
            'balance' => $this->subtotal,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'coupons' => implode(', ', $this->coupons()->pluck('code')->toArray()),
        ];
    }
}
