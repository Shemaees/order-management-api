<?php

namespace App\Modules\Payment\Database\Seeders;

use App\Modules\Order\Enums\OrderStatusEnum;
use App\Modules\Order\Models\Order;
use App\Modules\Payment\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::where('status', OrderStatusEnum::COMPLETED)->get();

        foreach ($orders as $order) {
            Payment::factory()->successful()->create([
                'order_id' => $order->id,
                'amount' => $order->total,
            ]);
        }
    }
}
