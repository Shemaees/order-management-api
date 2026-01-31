<?php

namespace App\Modules\Payment\Services\Gateways;

use App\Modules\Payment\DTOs\ProcessPaymentDTO;
use App\Modules\Payment\Enums\PaymentStatusEnum;
use App\Modules\Payment\Services\Gateways\Base\AbstractPaymentGateway;
use Illuminate\Support\Str;
use Random\RandomException;

class StripeGateway extends AbstractPaymentGateway
{
    public function getGatewayPrefix(): string
    {
        return 'ST';
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
                    'gateway' => 'stripe',
                    'message' => 'Stripe payment successful',
                    'charge_id' => 'ch_'.strtoupper(Str::random(24)),
                    'receipt_url' => 'https://stripe.com/receipt/'.Str::random(20),
                    'processed_at' => now()->toIso8601String(),
                ],
            ];
        }

        return [
            'success' => false,
            'payment_id' => $this->generatePaymentId(),
            'payment_status' => PaymentStatusEnum::FAILED,
            'transaction_details' => [
                'gateway' => 'stripe',
                'error' => 'Payment authentication failed',
                'error_code' => 'ST_AUTH_FAILED',
            ],
        ];
    }

    public function refund(string $transactionId, float $amount): array
    {
        $this->simulateApiCall();

        return [
            'success' => true,
            'refund_id' => 're_'.strtoupper(Str::random(24)),
            'amount' => $amount,
            'status' => 'refunded',
        ];
    }

    public function getStatus(string $transactionId): string
    {
        return 'successful';
    }
}
