<?php

namespace App\Modules\Order\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'items' => [
                'required',
                'array',
                'min:1',
            ],
            'items.*.product_id' => [
                'required',
                'exists:products,id',
            ],
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
            'notes' => [
                'nullable',
                'sometimes',
                'string',
                'max:1000',
            ],
            'billing_address' => [
                'nullable',
                'sometimes',
                'string',
                'max:500',
            ],
            'shipping_address' => [
                'nullable',
                'sometimes',
                'string',
                'max:500',
            ],
        ];
    }
}
