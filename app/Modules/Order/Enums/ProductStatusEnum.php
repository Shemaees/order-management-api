<?php

namespace App\Modules\Order\Enums;

enum ProductStatusEnum: int
{
    case Active = 1;
    case Inactive = 0;

    public static function values()
    {
        return array_map(fn ($value) => $value->value, self::cases());
    }
}
