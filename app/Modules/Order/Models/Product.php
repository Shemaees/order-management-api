<?php

namespace App\Modules\Order\Models;

use App\Modules\Order\Database\Factories\ProductFactory;
use App\Modules\Order\Enums\ProductStatusEnum;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @property int $id
 * @property float $price
 * @property float $discount
 * @property int $stock
 * @property string $image
 * @property ProductStatusEnum $status
 * @property float $total
 * @property string $name
 */
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, SoftDeletes;

    protected static function newFactory()
    {
        return ProductFactory::new();
    }

    protected $fillable = [
        'name',
        'price',
        'description',
        'discount',
        'stock',
        'image',
        'status',
    ];

    protected $casts = [
        'status' => ProductStatusEnum::class,
    ];

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    #[Scope]
    protected function available(Builder $query): Builder
    {
        return $query->where([
            [
                'stock', '>', 0,
            ],
            [
                'status', '=', ProductStatusEnum::Active,
            ],
        ]);
    }
}
