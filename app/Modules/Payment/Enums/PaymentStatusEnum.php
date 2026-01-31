<?php

namespace App\Modules\Payment\Enums;

enum PaymentStatusEnum: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';

    public static function values()
    {
        return array_map(fn ($value) => $value->value, self::cases());
    }
}
