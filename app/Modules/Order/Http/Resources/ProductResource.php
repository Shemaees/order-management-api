<?php

namespace App\Modules\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'stock' => $this->resource->stock,
            'status' => $this->resource->status,
            'image' => $this->resource->image,
            'created_at' => $this->resource->created_at->toDateTimeString(),
        ];
    }
}
