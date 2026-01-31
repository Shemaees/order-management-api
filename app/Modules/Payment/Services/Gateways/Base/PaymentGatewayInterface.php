<?php

namespace App\Modules\Payment\Services\Gateways\Base;

use App\Modules\Payment\DTOs\ProcessPaymentDTO;

interface PaymentGatewayInterface
{
    public function process(ProcessPaymentDTO $dto): array;

    public function refund(string $transactionId, float $amount): array;

    public function getStatus(string $transactionId): string;

    public function validate(ProcessPaymentDTO $dto): bool;
}
