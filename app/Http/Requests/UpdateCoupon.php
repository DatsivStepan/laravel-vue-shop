<?php

namespace App\Http\Requests;

use App\Rules\CouponType;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCoupon extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => 'max:255|unique:coupons,code,' . $this->id,
            'timestart' => 'nullable|date',
            'timeend' => 'nullable|date|after:timestart',
            'usedperuser' => 'integer|min:0',
            'usedcount' => 'integer|min:0',
            'status' => 'boolean',
            'type' => ['integer', new CouponType()],
            'discount' => 'numeric|min:0',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(['errors' => $validator->errors()], 422)
        );
    }
}
