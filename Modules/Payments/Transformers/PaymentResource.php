<?php

namespace Modules\Payments\Transformers;

use App\Classes\Enum\Order\PaymentType;
use App\Helpers\DateHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $res = [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'webhook' => $this->getWebhookRoute($this->type),
            'settings' => $this->settings,
            'currency' => $this->currency,
            'status' => $this->status,
            'created_at' => DateHelper::dateFormat($this->created_at),
            'updated_at' => DateHelper::dateFormat($this->updated_at),
        ];

        if (is_null($res['currency'])) {
            $res['currency'] = '';
        }

        return $res;
    }

    private function getWebhookRoute(string $type)
    {
        switch ($type) {
            case PaymentType::PAYPAL:
                return route('api.payments.paypal.webhook', ['tenant_id' => tenant('id')]);
            case PaymentType::STRIPE:
                return route('api.payments.stripe.webhook', ['tenant_id' => tenant('id')]);
            default:
                return '';
        }
    }
}
