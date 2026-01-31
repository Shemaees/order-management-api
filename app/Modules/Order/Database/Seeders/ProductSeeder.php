<?php

namespace App\Modules\Order\Database\Seeders;

use App\Modules\Order\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the Database seeds.
     */
    public function run(): void
    {
        Product::factory()->count(100)->create();
    }
}
