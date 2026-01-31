<?php

namespace App\Modules\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'product' => $this->whenLoaded(
                'product',
                fn () => ProductResource::make($this->resource->product)
            ),
            'quantity' => $this->resource->quantity,
            'price' => $this->resource->price,
            'discount' => $this->resource->discount,
            'total' => $this->resource->total,
        ];
    }
}
