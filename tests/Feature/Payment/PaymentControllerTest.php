<?php

namespace Tests\Feature\Payment;

use App\Models\User;
use App\Modules\Order\Enums\OrderStatusEnum;
use App\Modules\Order\Models\Order;
use App\Modules\Payment\Enums\PaymentMethodEnum;
use App\Modules\Payment\Enums\PaymentStatusEnum;
use App\Modules\Payment\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = auth('api')->login($this->user);
    }

    public function test_user_can_process_payment_for_confirmed_order(): void
    {
        $order = Order::factory()
            ->for($this->user)
            ->create([
                'status' => OrderStatusEnum::COMPLETED->value,
                'total' => 150.00,
            ]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->postJson('/api/orders/'.$order->id.'/payment', [
            'payment_method' => PaymentMethodEnum::CREDIT_CARD->value,
            'amount' => 150.00,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'order_id',
                    'payment_id',
                    'payment_method',
                    'payment_status',
                    'amount',
                ],
            ]);

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'payment_method' => 'credit_card',
            'amount' => 150.00,
        ]);
    }

    public function test_cannot_process_payment_for_pending_order(): void
    {
        $order = Order::factory()
            ->for($this->user)
            ->create(['status' => OrderStatusEnum::PENDING->value]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->postJson('/api/orders/'.$order->id.'/payment', [
            'order_id' => $order->id,
            'payment_method' => PaymentMethodEnum::CREDIT_CARD->value,
            'amount' => 100.00,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => false,
                'message' => 'Payments can only be processed for confirmed orders',
            ]);
    }

    public function test_validation_fails_with_invalid_payment_method(): void
    {
        $order = Order::factory()
            ->for($this->user)
            ->create(['status' => OrderStatusEnum::COMPLETED->value]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->postJson('/api/orders/'.$order->id.'/payment', [
            'order_id' => $order->id,
            'payment_method' => 'bitcoin',
            'amount' => 100.00,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['payment_method']);
    }

    public function test_user_can_view_payment_details(): void
    {
        $order = Order::factory()
            ->for($this->user)
            ->create(['status' => OrderStatusEnum::COMPLETED->value]);

        $payment = Payment::factory()
            ->successful()
            ->create(['order_id' => $order->id]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson("/api/payments/{$payment->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'id' => $payment->id,
                    'payment_status' => PaymentStatusEnum::COMPLETED->value,
                ],
            ]);
    }

    public function test_user_can_get_order_payments(): void
    {
        $order = Order::factory()
            ->for($this->user)
            ->create(['status' => OrderStatusEnum::COMPLETED->value]);

        Payment::factory()
            ->count(2)
            ->create(['order_id' => $order->id]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson("/api/payments/order/{$order->id}");

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data.data'));
    }
}
