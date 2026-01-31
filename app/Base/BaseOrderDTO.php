<?php

namespace App\Base;

use App\Modules\Order\DTOs\OrderItemDTO;

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
}
