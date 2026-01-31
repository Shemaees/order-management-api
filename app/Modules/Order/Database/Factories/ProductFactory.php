<?php

namespace App\Modules\Order\Database\Factories;

use App\Modules\Order\Enums\ProductStatusEnum;
use App\Modules\Order\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 1, 100),
            'discount' => $this->faker->boolean(20) ?
                $this->faker->randomFloat(2, 0, 10) :
                0,
            'stock' => $this->faker->numberBetween(0, 100),
            'image' => $this->faker->imageUrl(),
            'status' => $this->faker->randomElement(ProductStatusEnum::cases())->value,
        ];
    }
}
