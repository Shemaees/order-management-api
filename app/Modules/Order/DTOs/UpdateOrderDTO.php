<?php

namespace App\Modules\Order\DTOs;

use App\Base\BaseOrderDTO;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Models\Product;
use Illuminate\Http\Request;

class UpdateOrderDTO extends BaseOrderDTO
{
    /**
     * Constructor.
     */
    public function __construct(
        public Order $order,
        public int $user_id,
        public array $items,
        public ?string $notes,
        public ?string $billing_address,
        public ?string $shipping_address,
    ) {}

    /**
     * Create DTO from request
     */
    public static function fromRequest(array $request): static
    {
        $items = collect($request['items'])->map(function ($item) {
            $product = Product::find($item['product_id']);
            $item['price'] = $product->price;
            $item['discount'] = $product->discount;
            $item['total'] = $item['quantity'] * ($product->price - $product->discount);
            return OrderItemDTO::fromRequest($item);
        })->toArray();

        return new static(
            order: $request['order'],
            user_id: auth('api')->id(),
            items: $items,
            notes: $request['notes'] ?? null,
            billing_address: $request['billing_address'] ?? null,
            shipping_address: $request['shipping_address'] ?? null,
        );
    }
}
