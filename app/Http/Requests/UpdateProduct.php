<?php

namespace App\Http\Requests;

use App\Classes\Enum\Order\BillingType;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProduct extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $subscription = BillingType::SUBSCRIPTION;
        $installment = BillingType::INSTALLMENT;

        return [
            'name' => ['string', 'max:255'],
            'price' => 'required|numeric|min:0',
            'seats' => 'numeric|min:0',
            'max_seats_per_user' => 'numeric|min:0',
            'id_number' => 'nullable|unique:products,id_number,' . request('id'),
            'featured' => ['boolean'],

            'billing_type' => 'required|string',
            'billing_cycles' => "required_if:billing_type,$subscription|nullable|integer|min:0",
            'subscription_expiration_action' => "required_if:billing_type,$subscription|integer",
            'subscription_cancellation_action' => "required_if:billing_type,$subscription|integer",

            'installment' => "required_if:billing_type,$installment|array|nullable",
            'installment.recurring_period' => "required_if:billing_type,$installment|nullable|string",
            'installment.billing_cycles' => "required_if:billing_type,$installment|nullable|integer|min:1",
            'installment.fee' => "required_if:billing_type,$installment|nullable|numeric|min:0",
            'installment.fee_type' => "required_if:billing_type,$installment|nullable|integer",

            'customFields' => function ($attribute, $value, $fail) {
                $reqError = [];
                foreach ($value as $key => $va) {
                    if ($va['required'] && !$va['value']) {
                        $reqError[] = [$va['name'] => $va['title'] . ' is required'];
                    }
                }
                if ($reqError) {
                    $fail($reqError);
                }
            }
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(['errors' => $validator->errors()], 422)
        );
    }
}
