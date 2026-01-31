<?php

namespace App\Modules\Payment\Http\Resources;

use App\Modules\Order\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'order_id' => $this->resource->order_id,
            'payment_id' => $this->resource->payment_id,
            'payment_method' => $this->resource->payment_method,
            'payment_status' => $this->resource->payment_status?->value,
            'amount' => $this->resource->amount,
            'currency' => $this->resource->currency,
            'transaction_details' => $this->resource->transaction_details,
            'paid_at' => $this->resource->paid_at?->toDateTimeString(),
            'created_at' => $this->resource->created_at?->toDateTimeString(),
            'updated_at' => $this->resource->updated_at?->toDateTimeString(),
            'order' => $this->whenLoaded(
                'order',
                fn () => OrderResource::make($this->resource->order)
            ),
        ];
    }
}
