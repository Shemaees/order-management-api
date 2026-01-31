<?php

namespace App\Modules\Order\Http\Resources;

use App\Modules\Auth\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'user' => $this->whenLoaded(
                'user',
                fn () => UserResource::make($this->resource->user)
            ),
            'items' => $this->whenLoaded(
                'items',
                fn () => OrderItemResource::collection($this->resource->items)
            ),
            'notes' => $this->resource->notes,
            'billing_address' => $this->resource->billing_address,
            'shipping_address' => $this->resource->shipping_address,
            'status' => $this->resource->status,
            'created_at' => $this->resource->created_at->toDateTimeString(),
            'updated_at' => $this->resource->updated_at->toDateTimeString(),
            'sub_total' => $this->resource->sub_total,
            'tax' => $this->resource->tax,
            'discount' => $this->resource->discount,
            'total' => $this->resource->total,
        ];
    }
}
