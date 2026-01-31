<?php

namespace App\Modules\Payment\Enums;

enum PaymentMethodEnum: string
{
    case CREDIT_CARD = 'credit_card';
    case PAYPAL = 'paypal';
    case STRIPE = 'stripe';

    public static function values()
    {
        return array_map(fn ($value) => $value->value, self::cases());
    }
}
