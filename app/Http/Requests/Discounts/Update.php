<?php

namespace App\Http\Requests\Discounts;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
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
            'name' => 'string',
            'discount' => 'numeric|min:0',
            'time_start' => 'nullable|date',
            'time_end' => 'nullable|date|after:time_start',
            'used_per_user' => 'integer|min:0',
            'max_applied_products' => 'integer|min:0',
            'status' => 'boolean',
            'type' => 'integer',
            'min_number' => 'nullable|integer|min:0',
            'max_number' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ];
    }
}
