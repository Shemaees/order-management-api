<?php

namespace App\Modules\Payment\Services\Gateways;

use App\Modules\Payment\DTOs\ProcessPaymentDTO;
use App\Modules\Payment\Enums\PaymentStatusEnum;
use App\Modules\Payment\Services\Gateways\Base\AbstractPaymentGateway;
use Illuminate\Support\Str;
use Random\RandomException;

class CreditCardGateway extends AbstractPaymentGateway
{
    public function getGatewayPrefix(): string
    {
        return 'CC';
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
                    'gateway' => 'credit_card',
                    'message' => 'Payment processed successfully',
                    'card_last_four' => '****'.random_int(1000, 9999),
                    'reference_number' => 'REF_'.strtoupper(Str::random(12)),
                    'processed_at' => now()->toIso8601String(),
                ],
            ];
        }

        return [
            'success' => false,
            'payment_id' => $this->generatePaymentId(),
            'payment_status' => PaymentStatusEnum::FAILED,
            'transaction_details' => [
                'gateway' => 'credit_card',
                'error' => 'Card declined',
                'error_code' => 'CC_DECLINED',
            ],
        ];
    }

    public function refund(string $transactionId, float $amount): array
    {
        $this->simulateApiCall();

        return [
            'success' => true,
            'refund_id' => 'REF_'.strtoupper(Str::random(16)),
            'amount' => $amount,
            'status' => 'refunded',
        ];
    }

    public function getStatus(string $transactionId): string
    {
        return 'successful';
    }
}
