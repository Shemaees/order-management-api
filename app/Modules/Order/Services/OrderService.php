<?php

namespace App\Modules\Order\Services;

use App\Base\BaseService;
use App\Modules\Order\DTOs\CreateOrderDTO;
use App\Modules\Order\DTOs\OrderFilterDTO;
use App\Modules\Order\DTOs\UpdateOrderDTO;
use App\Modules\Order\Enums\OrderStatusEnum;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Repositories\OrderRepository;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderService extends BaseService
{
    public function __construct(public OrderRepository $orderRepository) {}

    /**
     * @param OrderFilterDTO $orderFilterDTO
     * @return LengthAwarePaginator|void
     * @throws \Throwable
     */
    public function listOrders(
        OrderFilterDTO $orderFilterDTO,
    )
    {
        try {
            return $this->orderRepository->getAllOrders(
                $orderFilterDTO
            );
        } catch (\Throwable $exception) {
            $this->handleException($exception);
        }
    }

    /**
     * @param CreateOrderDTO $DTO
     * @return Order|void
     * @throws \Throwable
     */
    public function createOrder(CreateOrderDTO $DTO)
    {
        try {
            $itemData = $DTO->items;
            $orderData = $DTO->toArray();
            $orderData['user_id'] = $DTO->user_id;
            $orderData['billing_address'] = $DTO->billing_address;
            $orderData['shipping_address'] = $DTO->shipping_address;
            $orderData['notes'] = $DTO->notes;
            $orderData['status'] = OrderStatusEnum::PENDING;
            $orderData['total'] = $DTO->calculateTotal();
            $orderData['sub_total'] = $DTO->calculateSubTotal();
            $orderData['tax'] = $DTO->calculateTax();
            $orderData['discount'] = $DTO->calculateDiscount();

            return $this->orderRepository->createWithItems($orderData, $itemData);
        } catch (\Throwable $exception) {
            $this->handleException($exception);
        }
    }

    /**
     * @param UpdateOrderDTO $DTO
     * @return Order|void
     * @throws \Throwable
     */
    public function updateOrder(UpdateOrderDTO $DTO)
    {
        try {
            return $this->orderRepository->updateWithItems(
                $DTO->order,
                $DTO->toArray(),
                $DTO->items
            );

        } catch (\Throwable $exception) {
            $this->handleException($exception);
        }
    }

    public function deleteOrder(Order $order)
    {
        throw new Exception('Not implemented');
    }

    /**
     * @param Order $order
     * @param string $status
     * @return bool|void
     * @throws \Throwable
     */
    public function updateOrderStatus(Order $order, string $status)
    {
        try {
            $this->validateStatusTransition($order->status, $status);

            return $this->orderRepository->updateStatus($order, $status);
        } catch (\Throwable $exception) {
            $this->handleException($exception);
        }
    }

    private function validateStatusTransition(OrderStatusEnum $currentStatus, string $newStatus): void
    {
        if ($currentStatus === OrderStatusEnum::CANCELED) {
            throw new Exception(
                'Cannot change status of a cancelled order',
                400
            );
        }
        if ($currentStatus === OrderStatusEnum::COMPLETED && $newStatus === OrderStatusEnum::PENDING->value) {
            throw new Exception(
                'Cannot change confirmed order back to pending',
                400
            );
        }
        if ($currentStatus->value === $newStatus) {
            throw new Exception(
                "Order is already in {$newStatus} status",
                400
            );
        }
    }
}
