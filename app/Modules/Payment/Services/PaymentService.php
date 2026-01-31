<?php

namespace App\Modules\Payment\Services;

use App\Base\BaseService;
use App\Modules\Order\Enums\OrderStatusEnum;
use App\Modules\Order\Repositories\OrderRepository;
use App\Modules\Payment\DTOs\ProcessPaymentDTO;
use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Repositories\PaymentRepository;
use App\Modules\Payment\Services\Gateways\PaymentGatewayFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

class PaymentService extends BaseService
{
    public function __construct(
        protected PaymentRepository $paymentRepository,
        protected OrderRepository $orderRepository,
        protected PaymentGatewayFactory $gatewayFactory
    ) {}

    /**
     * @return Payment|Model|void
     *
     * @throws \Throwable
     */
    public function processPayment(ProcessPaymentDTO $dto)
    {
        try {
            $order = $dto->order;
            if ($order->status !== OrderStatusEnum::COMPLETED) {
                throw new RuntimeException(
                    'Payments can only be processed for confirmed orders',
                    400
                );
            }

            if (! PaymentGatewayFactory::isSupported($dto->payment_method)) {
                throw new RuntimeException(
                    "Unsupported payment method: {$dto->payment_method}",
                    400
                );
            }
            $gateway = $this->gatewayFactory->make($dto->payment_method);

            if (! $gateway->validate($dto)) {
                throw new RuntimeException('Invalid payment details', 400);
            }
            $result = $gateway->process($dto);

            return $this->paymentRepository->create([
                'order_id' => $order->id,
                'payment_id' => $result['payment_id'],
                'payment_method' => $dto->payment_method,
                'payment_status' => $result['payment_status'],
                'amount' => $dto->amount,
                'currency' => $dto->currency,
                'transaction_details' => $result['transaction_details'],
                'paid_at' => $result['success'] ? now() : null,
            ]);
        } catch (\Throwable $exception) {
            $this->handleException($exception);
        }
    }

    /**
     * @return Collection|Model|void
     *
     * @throws \Throwable
     */
    public function getPayment(int $id)
    {
        try {
            $payment = $this->paymentRepository->findWithOrder($id);

            if (! $payment) {
                throw new RuntimeException('Payment not found');
            }

            return $payment;
        } catch (\Throwable $exception) {
            $this->handleException($exception);
        }
    }

    /**
     * @return mixed|void
     *
     * @throws \Throwable
     */
    public function getOrderPayments(int $orderId)
    {
        try {
            return $this->paymentRepository->getByOrder($orderId);
        } catch (\Throwable $exception) {
            $this->handleException($exception);
        }
    }
}
