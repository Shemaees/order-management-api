<?php

namespace App\Modules\Payment\Services\Gateways;

use App\Modules\Payment\Services\Gateways\Base\PaymentGatewayInterface;

class PaymentGatewayFactory
{
    private const GATEWAYS = [
        'credit_card' => CreditCardGateway::class,
        'paypal' => PayPalGateway::class,
        'stripe' => StripeGateway::class,
    ];

    /**
     * @throws \Exception
     */
    public function make(string $method): PaymentGatewayInterface
    {
        if (! isset(self::GATEWAYS[$method])) {
            throw new \Exception("Unsupported payment method: {$method}", 400);
        }

        $gatewayClass = self::GATEWAYS[$method];

        return app($gatewayClass);
    }

    public static function getAvailableMethods(): array
    {
        return array_keys(self::GATEWAYS);
    }

    public static function isSupported(string $method): bool
    {
        return isset(self::GATEWAYS[$method]);
    }
}
