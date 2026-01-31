<?php

namespace App\Modules\Order\Database\Factories;

use App\Models\User;
use App\Modules\Order\Enums\OrderStatusEnum;
use App\Modules\Order\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Random\RandomException;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     *
     * @throws RandomException
     */
    public function definition(): array
    {
        $discount = 0;
        $subtotal = random_int(100, 1000);
        $tax = (($subtotal - $discount) * 14) / 100;
        $total = $subtotal + $tax - $discount;

        return [
            'user_id' => User::factory(),
            'status' => OrderStatusEnum::PENDING,
            'discount' => 0,
            'sub_total' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'billing_address' => [
                'address' => $this->faker->address(),
                'city' => $this->faker->city(),
                'zip' => $this->faker->postcode(),
            ],
            'shipping_address' => [
                'address' => $this->faker->address(),
                'city' => $this->faker->city(),
                'zip' => $this->faker->postcode(),
            ],
            'payment_method' => 'cash',
            'payment_details' => [
                'method' => 'cash',
                'status' => 'pending',
            ],
            'notes' => $this->faker->optional()->text(),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatusEnum::COMPLETED,
            'completed_at' => now(),
        ]);
    }

    public function canceled()
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatusEnum::CANCELED,
            'canceled_at' => now(),
        ]);
    }
}
