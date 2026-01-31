<?php

namespace App\Modules\Payment\DTOs;

use App\Base\BaseDTO;
use App\Modules\Order\Models\Order;

class ProcessPaymentDTO extends BaseDTO
{
    public function __construct(
        public Order $order,
        public string $payment_method,
        public float $amount,
        public string $currency = 'USD',
        public ?array $payment_details = null
    ) {}

    public static function fromRequest(array $request): static
    {
        return new static(
            order: $request['Order'],
            payment_method: $request['payment_method'],
            amount: (float) $request['amount'],
            currency: $request['currency'] ?? 'USD',
            payment_details: $request['payment_details'] ?? [],
        );
    }
}
