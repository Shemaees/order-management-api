<?php

namespace App\Modules\Payment\Http\Requests;

use App\Modules\Payment\Services\Gateways\PaymentGatewayFactory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProcessPaymentRequest extends FormRequest
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
            'payment_method' => [
                'required',
                'string',
                Rule::in(PaymentGatewayFactory::getAvailableMethods()),
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],
            'currency' => [
                'nullable',
                'string',
                'max:3',
            ],
            'payment_details' => [
                'nullable',
                'array',
            ],
        ];
    }
}
