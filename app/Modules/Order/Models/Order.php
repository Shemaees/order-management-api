<?php

namespace App\Modules\Order\Models;

use App\Models\User;
use App\Modules\Order\Database\Factories\OrderFactory;
use App\Modules\Order\Enums\OrderStatusEnum;
use App\Modules\Payment\Models\Payment;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed $status
 * @property mixed $id
 * @property mixed $user_id
 * @property float $total
 */
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory, SoftDeletes;

    protected static function newFactory()
    {
        return OrderFactory::new();
    }

    protected $fillable = [
        'user_id',
        'status',
        'total',
        'sub_total',
        'tax',
        'discount',
        'billing_address',
        'shipping_address',
        'payment_method',
        'payment_details',
        'notes',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'status' => OrderStatusEnum::class,
        'billing_address' => 'json',
        'shipping_address' => 'json',
        'payment_details' => 'json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(
            Product::class,
            OrderItem::class,
            'order_id',
            'id',
            'id',
            'product_id',
        );
    }

    public function isCanceled(): bool
    {
        return $this->status === OrderStatusEnum::CANCELED;
    }

    public function isCompleted(): bool
    {
        return $this->status === OrderStatusEnum::COMPLETED;
    }

    public function isPending(): bool
    {
        return $this->status === OrderStatusEnum::PENDING;
    }

    #[Scope]
    protected function completed(Builder $query): Builder
    {
        return $query->where('status', OrderStatusEnum::COMPLETED);
    }

    #[Scope]
    protected function pending(Builder $query): Builder
    {
        return $query->where('status', OrderStatusEnum::PENDING);
    }

    #[Scope]
    protected function canceled(Builder $query): Builder
    {
        return $query->where('status', OrderStatusEnum::CANCELED);
    }

    #[Scope]
    protected function forUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
