<?php

namespace App\Modules\Order\DTOs;

use App\Base\BaseDTO;
use App\Modules\Order\Models\Product;
use Exception;

class OrderItemDTO extends BaseDTO
{
    /**
     * Constructor.
     */
    public function __construct(
        public int $product_id,
        public int $quantity,
        public float $price,
        public float $discount,
        public ?float $total = null,
    ) {
        try {
            $this->checkProductAvailabilty();
        } catch (Exception $exception) {
            $product = Product::find($this->product_id);

            throw new Exception(
                'Product ' . $product->name . ' stock is not enough'
            );
        }
    }

    /**
     * Create DTO from request
     */
    public static function fromRequest(array $request): static
    {
        return new static(
            product_id: $request['product_id'],
            quantity: $request['quantity'],
            price: $request['price'],
            discount: $request['discount'],
            total: $request['total'],
        );
    }

    public function calculateTotal(): float
    {
        return $this->quantity * ($this->price - $this->discount);
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'discount' => $this->discount,
            'total' => $this->total ?? $this->calculateTotal(),
        ];
    }

    public function checkProductAvailabilty()
    {
        return Product::find($this->product_id)->stock >= $this->quantity;
    }
}
