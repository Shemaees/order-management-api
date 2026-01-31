<?php

namespace App\Modules\Payment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Order\Models\Order;
use App\Modules\Payment\DTOs\ProcessPaymentDTO;
use App\Modules\Payment\Http\Requests\ProcessPaymentRequest;
use App\Modules\Payment\Http\Resources\PaymentResource;
use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Services\PaymentService;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    use ApiResponseTrait;

    public function __construct(public PaymentService $paymentService) {}

    /**
     * @throws \Throwable
     */
    public function store(Order $order, ProcessPaymentRequest $request): ?JsonResponse
    {
        try {
            $data = $request->validated();
            $data['Order'] = $order;
            $dto = ProcessPaymentDTO::fromRequest($data);
            /** @var Payment $payment */
            $payment = $this->paymentService->processPayment($dto);

            $statusCode = $payment->isSuccessful() ? 200 : 400;
            $message = $payment->isSuccessful()
                ? 'Payment processed successfully'
                : 'Payment failed';

            return $this->successResponse(
                new PaymentResource($payment),
                $message,
                $statusCode
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                $exception->getMessage(),
                $exception->getCode() ?: 401
            );
        }
    }

    /**
     * @throws \Throwable
     */
    public function show(Payment $payment): JsonResponse
    {
        try {
            return $this->successResponse(
                new PaymentResource($payment->load([
                    'order',
                    'order.items',
                ])),
                'Payment retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                $e->getCode() ?: 400
            );
        }
    }

    /**
     * @throws \Throwable
     */
    public function orderPayments(Order $order): JsonResponse
    {
        try {
            $payments = $order->payments()->get();

            return $this->successResponse(
                PaymentResource::collection($payments)->response()->getData(true),
                'Order payments retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                $e->getCode() ?: 400
            );
        }
    }
}
