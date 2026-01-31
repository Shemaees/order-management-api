<?php

namespace App\Modules\Payment\Services\Gateways\Base;

use App\Modules\Payment\DTOs\ProcessPaymentDTO;
use Illuminate\Support\Str;

abstract class AbstractPaymentGateway implements PaymentGatewayInterface
{
    protected function generatePaymentId(): string
    {
        return strtoupper($this->getGatewayPrefix().'_'.Str::random(16));
    }

    abstract protected function getGatewayPrefix(): string;

    protected function simulateApiCall(): void
    {
        usleep(100000);
    }

    public function validate(ProcessPaymentDTO $dto): bool
    {
        return $dto->amount > 0;
    }
}
