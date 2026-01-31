<?php

namespace App\Modules\Payment\Models;

use App\Modules\Order\Models\Order;
use App\Modules\Payment\Database\Factories\PaymentFactory;
use App\Modules\Payment\Enums\PaymentStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property PaymentStatusEnum $payment_status
 */
class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_id',
        'payment_method',
        'payment_status',
        'amount',
        'currency',
        'transaction_details',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'transaction_details' => 'array',
        'payment_status' => PaymentStatusEnum::class,
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    protected static function newFactory()
    {
        return PaymentFactory::new();
    }

    public function isPending(): bool
    {
        return $this->payment_status === PaymentStatusEnum::PENDING;
    }

    public function isSuccessful(): bool
    {
        return $this->payment_status === PaymentStatusEnum::COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->payment_status === PaymentStatusEnum::FAILED;
    }
}
