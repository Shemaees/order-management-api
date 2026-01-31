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
        return new static(
            order: $request['order'],
            user_id: auth('api')->id(),
            items: self::itemsFormatting($request['items']),
            notes: $request['notes'] ?? null,
            billing_address: $request['billing_address'] ?? null,
            shipping_address: $request['shipping_address'] ?? null,
        );
    }
}
