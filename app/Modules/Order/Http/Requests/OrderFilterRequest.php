<?php

namespace App\Modules\Order\Http\Requests;

use App\Modules\Order\Enums\OrderStatusEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class OrderFilterRequest extends FormRequest
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
            'status' => [
                'nullable',
                'sometimes',
                'string',
                'in:'.implode(',', OrderStatusEnum::values()),
            ],
            'from_date' => [
                'nullable',
                'sometimes',
                'date_format:Y-m-d',
            ],
            'to_date' => [
                'nullable',
                'sometimes',
                'required_with:from_date',
                'date_format:Y-m-d',
                'after_or_equal:from_date',
            ],
            'per_page' => [
                'sometimes',
                'nullable',
                'integer',
                'min:10',
                'max:100',
            ],
            'page' => [
                'sometimes',
                'nullable',
                'integer',
                'min:1',
            ],
        ];
    }
}
