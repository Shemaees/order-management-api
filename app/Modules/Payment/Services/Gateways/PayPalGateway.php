<?php

namespace App\Modules\Payment\Services\Gateways;

use App\Modules\Payment\DTOs\ProcessPaymentDTO;
use App\Modules\Payment\Enums\PaymentStatusEnum;
use App\Modules\Payment\Services\Gateways\Base\AbstractPaymentGateway;
use Illuminate\Support\Str;
use Random\RandomException;

class PayPalGateway extends AbstractPaymentGateway
{
    public function getGatewayPrefix(): string
    {
        return 'PP';
    }

    /**
     * @throws RandomException
     */
    public function process(ProcessPaymentDTO $dto): array
    {
        $this->simulateApiCall();
        $isSuccessful = random_int(1, 10) <= 9;

        if ($isSuccessful) {
            return [
                'success' => true,
                'payment_id' => $this->generatePaymentId(),
                'payment_status' => PaymentStatusEnum::COMPLETED,
                'transaction_details' => [
                    'gateway' => 'paypal',
                    'message' => 'PayPal payment completed',
                    'payer_email' => 'user@example.com',
                    'transaction_id' => strtoupper(Str::random(20)),
                    'processed_at' => now()->toIso8601String(),
                ],
            ];
        }

        return [
            'success' => false,
            'payment_id' => $this->generatePaymentId(),
            'payment_status' => PaymentStatusEnum::FAILED,
            'transaction_details' => [
                'gateway' => 'paypal',
                'error' => 'Insufficient funds in PayPal account',
                'error_code' => 'PP_INSUFFICIENT_FUNDS',
            ],
        ];
    }

    public function refund(string $transactionId, float $amount): array
    {
        $this->simulateApiCall();

        return [
            'success' => true,
            'refund_id' => 'PPREF_'.strtoupper(Str::random(16)),
            'amount' => $amount,
            'status' => 'refunded',
        ];
    }

    public function getStatus(string $transactionId): string
    {
        return 'successful';
    }
}
