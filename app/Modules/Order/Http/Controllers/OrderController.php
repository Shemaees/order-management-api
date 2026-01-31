<?php

namespace App\Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Order\DTOs\CreateOrderDTO;
use App\Modules\Order\DTOs\OrderFilterDTO;
use App\Modules\Order\DTOs\UpdateOrderDTO;
use App\Modules\Order\Http\Requests\CreateOrderRequest;
use App\Modules\Order\Http\Requests\OrderFilterRequest;
use App\Modules\Order\Http\Requests\UpdateOrderRequest;
use App\Modules\Order\Http\Requests\UpdateOrderStatusRequest;
use App\Modules\Order\Http\Resources\OrderResource;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Services\OrderService;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    use ApiResponseTrait;

    public function __construct(public OrderService $orderService) {}

    /**
     * @throws \Throwable
     */
    public function index(OrderFilterRequest $request)
    {
        try {
            return $this->successResponse(
                OrderResource::collection(
                    $this->orderService->listOrders(
                        OrderFilterDTO::fromRequest($request->validated())
                    )
                )->response()->getData(true)
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
    public function store(CreateOrderRequest $request): ?JsonResponse
    {
        try {
            $DTO = CreateOrderDTO::fromRequest($request->validated());

            return $this->successResponse(
                OrderResource::make(
                    $this->orderService->createOrder($DTO)
                )
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                $exception->getMessage(),
                $exception->getCode() ?: 401
            );
        }
    }

    public function show(Order $order)
    {
        try {
            if (auth('api')->id() !== $order->user_id) {
                return $this->errorResponse(
                    'Unauthorized',
                    401
                );
            }

            return $this->successResponse(
                OrderResource::make($order->load([
                    'items',
                    'items.product',
                    'user',
                ]))
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
    public function update(Order $order, UpdateOrderRequest $request): ?JsonResponse
    {
        try {
            $DTO = UpdateOrderDTO::fromRequest(
                array_merge($request->validated(), ['order' => $order])
            );

            return $this->successResponse(
                OrderResource::make(
                    $this->orderService->updateOrder($DTO)
                )
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                $exception->getMessage(),
                $exception->getCode() ?: 401
            );
        }
    }

    public function destroy(Order $order)
    {
        try {
            $this->orderService->deleteOrder($order);

            return $this->successResponse(
                'Order deleted successfully'
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
    public function updateOrderStatus(Order $order, UpdateOrderStatusRequest $request)
    {
        try {

            if ($this->orderService->updateOrderStatus(
                $order,
                $request->validated('status'),
            )) {
                return $this->successResponse(
                    'Order status updated successfully'
                );
            }

            return $this->errorResponse(
                'Failed to update order status',
                400
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                $exception->getMessage(),
                $exception->getCode() ?: 401
            );
        }
    }
}
