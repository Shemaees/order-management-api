<?php

namespace App\Base;

use App\Modules\Order\DTOs\OrderItemDTO;
use App\Modules\Order\Models\Product;

abstract class BaseOrderDTO extends BaseDTO
{
    public array $items;

    public function calculateSubTotal(): ?float
    {
        return collect($this->items)
            ->sum(fn (OrderItemDTO $item) => $item->calculateTotal());
    }

    public function calculateTax(): float
    {
        $subTotal = $this->calculateSubTotal();

        return $subTotal ? $subTotal * 0.14 : 0;
    }

    public function calculateDiscount(): ?float
    {
        return collect($this->items)
            ->sum(fn (OrderItemDTO $item) => $item->discount);
    }

    public function calculateTotal(): ?float
    {
        return $this->calculateSubTotal() + $this->calculateTax() - $this->calculateDiscount();
    }

    public static function itemsFormatting(array $items): array
    {
        return collect($items)->map(callback: function ($item) {
            $product = Product::find($item['product_id']);
            $item['price'] = $product->price;
            $item['discount'] = $product->discount;
            $item['total'] = $item['quantity'] * ($product->price - $product->discount);

            return OrderItemDTO::fromRequest($item);
        })->toArray();
    }
}
