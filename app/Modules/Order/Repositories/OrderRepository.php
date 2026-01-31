<?php

namespace App\Modules\Order\Repositories;

use App\Base\BaseRepository;
use App\Modules\Order\DTOs\OrderFilterDTO;
use App\Modules\Order\DTOs\OrderItemDTO;
use App\Modules\Order\Enums\OrderStatusEnum;
use App\Modules\Order\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderRepository extends BaseRepository
{
    public array $allRelations = [
        'items',
        'items.product',
        'user',
    ];

    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function getAllOrders(OrderFilterDTO $filterDTO)
    {
        return $this->model::where('user_id', auth('api')->id())
            ->when(
                $filterDTO->hasStatus(),
                fn ($query) => $query->where('status', OrderStatusEnum::from($filterDTO->status))
            )
            ->when(
                $filterDTO->hasDateRange(),
                function ($query) use ($filterDTO) {
                    if ($filterDTO->from_date) {
                        $query->whereDate('created_at', '>=', $filterDTO->from_date);
                    }
                    if ($filterDTO->to_date) {
                        $query->whereDate('created_at', '<=', $filterDTO->to_date);
                    }
                }
            )
            ->with($this->allRelations)
            ->latest()
            ->paginate($filterDTO->per_page);
    }

    /**
     * @throws \Throwable
     */
    public function createWithItems(array $orderData, array $itemData): Order
    {
        return DB::transaction(function () use ($orderData, $itemData) {
            $order = $this->create($orderData);
            if (! empty($itemData)) {
                $order->items()->createMany(
                    collect($itemData)
                        ->map(fn (OrderItemDTO $dto) => $dto->toArray())
                        ->all()
                );
            }

            return $order->load($this->allRelations);
        });
    }

    /**
     * @throws \Throwable
     */
    public function updateWithItems(Order $order, array $orderNewData, array $itemNewData): mixed
    {
        return DB::transaction(function () use ($order, $orderNewData, $itemNewData) {
            $order->update($orderNewData);

            if ($itemNewData !== null) {
                $order->items()->delete();
                $order->items()->createMany(
                    collect($itemNewData)
                        ->map(fn (OrderItemDTO $dto) => $dto->toArray())
                        ->all()
                );

            }

            return $order->load($this->allRelations);
        });
    }

    public function updateStatus(Order $order, string $status): bool
    {
        return $order->update(['status' => $status]);
    }

    public function findWithRelations(int $id): Order
    {
        return $this->model::where('id', $id)->with($this->allRelations)->first();
    }
}
