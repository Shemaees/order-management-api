<?php

namespace Database\Seeders;

use App\Modules\Order\Database\Seeders\OrderSeeder;
use App\Modules\Order\Database\Seeders\ProductSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's Database.
     */
    public function run(): void {
        $this->call([
            ProductSeeder::class,
//            OrderSeeder::class,
        ]);
    }
}
