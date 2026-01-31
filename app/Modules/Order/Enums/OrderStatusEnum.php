<?php

namespace App\Modules\Order\Enums;

enum OrderStatusEnum: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';

    public static function values()
    {
        return array_map(fn ($value) => $value->value, self::cases());
    }
}
