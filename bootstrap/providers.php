<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\PasswordServiceProvider::class,
    App\Modules\Order\Providers\OrderServiceProvider::class,
    App\Modules\Payment\Providers\PaymentServiceProvider::class,
];
