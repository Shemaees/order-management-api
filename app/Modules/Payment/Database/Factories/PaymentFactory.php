<?php

namespace App\Modules\Payment\Database\Factories;

use App\Modules\Order\Models\Order;
use App\Modules\Payment\Enums\PaymentMethodEnum;
use App\Modules\Payment\Enums\PaymentStatusEnum;
use App\Modules\Payment\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Payment\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'payment_id' => 'PAY_'.Str::upper(Str::random(16)),
            'payment_method' => fake()->randomElement(PaymentMethodEnum::values()),
            'payment_status' => PaymentStatusEnum::PENDING,
            'amount' => fake()->randomFloat(2, 10, 1000),
            'currency' => 'USD',
            'transaction_details' => [],
        ];
    }

    public function successful(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => PaymentStatusEnum::COMPLETED,
            'paid_at' => now(),
            'transaction_details' => [
                'gateway_response' => 'Payment processed successfully',
                'reference_number' => fake()->uuid(),
            ],
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => PaymentStatusEnum::FAILED,
            'transaction_details' => [
                'error' => 'Insufficient funds',
                'error_code' => 'E001',
            ],
        ]);
    }
}
