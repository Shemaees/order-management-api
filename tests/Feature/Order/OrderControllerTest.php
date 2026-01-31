<?php

namespace Tests\Feature\Order;

use App\Models\User;
use App\Modules\Order\Enums\OrderStatusEnum;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Models\OrderItem;
use App\Modules\Order\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderControllerTest extends TestCase
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

    /**
     * Test user can list their orders
     */
    public function test_user_can_list_their_orders(): void
    {
        // Create orders for the authenticated user
        Order::factory()
            ->for($this->user)
            ->has(OrderItem::factory()->count(2), 'items')
            ->count(3)
            ->create();

        // Create orders for another user (should not appear)
        Order::factory()
            ->count(2)
            ->create();

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson('/api/orders');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'user',
                            'items',
                            'notes',
                            'billing_address',
                            'shipping_address',
                            'status',
                            'created_at',
                            'updated_at',
                            'sub_total',
                            'tax',
                            'discount',
                            'total',
                        ],
                    ],
                    'links',
                    'meta' => [
                        'per_page',
                        'to'
                    ],
                ],
            ]);

        // Only user's orders should be returned
        $this->assertCount(3, $response->json('data.data'));
    }

    /**
     * Test user can filter orders by status
     */
    public function test_user_can_filter_orders_by_status(): void
    {
        Order::factory()
            ->for($this->user)
            ->count(2)
            ->create(['status' => OrderStatusEnum::PENDING->value]);

        Order::factory()
            ->for($this->user)
            ->count(3)
            ->create(['status' => OrderStatusEnum::COMPLETED->value]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson('/api/orders?status=pending');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data.data'));
    }

    /**
     * Test user can filter orders by date range
     */
    public function test_user_can_filter_orders_by_date_range(): void
    {
        Order::factory()
            ->for($this->user)
            ->create(['created_at' => now()->subDays(10)]);

        Order::factory()
            ->for($this->user)
            ->create(['created_at' => now()->subDays(5)]);

        Order::factory()
            ->for($this->user)
            ->create(['created_at' => now()]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson('/api/orders?from_date=' . now()->subDays(7)->toDateString() . '&to_date=' . now()->toDateString());

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data.data'));
    }

    /**
     * Test user can create order
     */
    public function test_user_can_create_order(): void
    {
        $product1 = Product::factory()->create(['price' => 100]);
        $product2 = Product::factory()->create(['price' => 50]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->postJson('/api/orders', [
            'items' => [
                [
                    'product_id' => $product1->id,
                    'quantity' => 2,
                ],
                [
                    'product_id' => $product2->id,
                    'quantity' => 1,
                ],
            ],
            'billing_address' => '123 Billing St',
            'shipping_address' => '456 Shipping Ave',
            'notes' => 'Test order',
            'tax_percentage' => 10,
            'discount_percentage' => 5,
        ]);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'user',
                    'status',
                    'total',
                    'sub_total',
                    'tax',
                    'discount',
                    'items' => [
                        '*' => [
                            'id',
                            'product',
                            'quantity',
                            'price',
                            'discount',
                            'total',
                        ],
                    ],
                ],
            ])
            ->assertJson([
                'status' => true,
                'data' => [
                    'user' => [
                        'id' => $this->user->id,
                        'name' => $this->user->name,
                        'email' => $this->user->email,
                    ],
                    'status' => 'pending',
                    'notes' => 'Test order',
                ],
            ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product1->id,
            'quantity' => 2,
        ]);
    }

    /**
     * Test order creation validation fails with empty items
     */
    public function test_order_creation_fails_with_empty_items(): void
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->postJson('/api/orders', [
            'items' => [],
            'billing_address' => '123 Billing St',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items']);
    }

    /**
     * Test order creation validation fails with invalid product
     */
    public function test_order_creation_fails_with_invalid_product(): void
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->postJson('/api/orders', [
            'items' => [
                [
                    'product_id' => 99999, // Non-existent product
                    'quantity' => 1,
                    'price' => 100,
                ],
            ],
            'billing_address' => '123 Billing St',
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test user can view their own order
     */
    public function test_user_can_view_their_own_order(): void
    {
        $order = Order::factory()
            ->for($this->user)
            ->has(OrderItem::factory()->count(2), 'items')
            ->create();

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'id',
                    'user',
                    'notes',
                    'billing_address',
                    'shipping_address',
                    'status',
                    'items',
                ],
            ])
            ->assertJson([
                'status' => true,
                'data' => [
                    'id' => $order->id,
                    'user' => [
                        'id' => $this->user->id,
                        'name' => $this->user->name,
                        'email' => $this->user->email,
                    ],
                ],
            ]);
    }

    /**
     * Test user cannot view another user's order
     */
    public function test_user_cannot_view_another_users_order(): void
    {
        $anotherUser = User::factory()->create();
        $order = Order::factory()
            ->for($anotherUser)
            ->create();

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson("/api/orders/{$order->id}");

        $response->assertStatus(401)
            ->assertJson([
                'status' => false,
                'message' => 'Unauthorized',
            ]);
    }

    /**
     * Test user can update their order
     */
    public function test_user_can_update_their_order(): void
    {
        $product = Product::factory()->create(['price' => 100]);

        $order = Order::factory()
            ->for($this->user)
            ->create(['status' => 'pending']);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->putJson("/api/orders/{$order->id}", [
            'notes' => 'Updated note',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 3,
                    'price' => 100,
                    'discount' => 5,
                    'total' => 295,
                ],
            ],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'id' => $order->id,
                    'notes' => 'Updated note',
                ],
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'notes' => 'Updated note',
        ]);
    }

    /**
     * Test user can delete their order
     */
//    public function test_user_can_delete_their_order(): void
//    {
//        $order = Order::factory()
//            ->for($this->user)
//            ->create();
//
//        $response = $this->withHeaders([
//            'Authorization' => "Bearer {$this->token}",
//        ])->deleteJson("/api/orders/{$order->id}");
//
//        $response->assertStatus(200)
//            ->assertJson([
//                'status' => true,
//            ]);
//
//        $this->assertDatabaseMissing('orders', [
//            'id' => $order->id,
//        ]);
//    }

    /**
     * Test user can update order status
     */
    public function test_user_can_update_order_status(): void
    {
        $order = Order::factory()
            ->for($this->user)
            ->create(['status' => 'pending']);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->patchJson("/api/orders/{$order->id}/status", [
            'status' => OrderStatusEnum::COMPLETED->value,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatusEnum::COMPLETED->value,
        ]);
    }

    /**
     * Test cannot update cancelled order status
     */
    public function test_cannot_update_cancelled_order_status(): void
    {
        $order = Order::factory()
            ->for($this->user)
            ->create(['status' => OrderStatusEnum::CANCELED->value]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->patchJson("/api/orders/{$order->id}/status", [
            'status' => OrderStatusEnum::COMPLETED->value,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => false,
                'message' => 'Cannot change status of a cancelled order',
            ]);
    }

    /**
     * Test cannot change confirmed order back to pending
     */
    public function test_cannot_change_confirmed_order_back_to_pending(): void
    {
        $order = Order::factory()
            ->for($this->user)
            ->create(['status' => OrderStatusEnum::COMPLETED]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->patchJson("/api/orders/{$order->id}/status", [
            'status' => OrderStatusEnum::PENDING,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => false,
                'message' => 'Cannot change confirmed order back to pending',
            ]);
    }

    /**
     * Test cannot set same status
     */
    public function test_cannot_set_same_status(): void
    {
        $order = Order::factory()
            ->for($this->user)
            ->create(['status' => OrderStatusEnum::PENDING->value]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->patchJson("/api/orders/{$order->id}/status", [
            'status' => OrderStatusEnum::PENDING->value,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => false,
                'message' => 'Order is already in pending status',
            ]);
    }

    /**
     * Test unauthenticated user cannot access orders
     */
    public function test_unauthenticated_user_cannot_access_orders(): void
    {
        $response = $this->getJson('/api/orders');

        $response->assertStatus(401);
    }

    /**
     * Test pagination works correctly
     */
    public function test_pagination_works_correctly(): void
    {
        Order::factory()
            ->for($this->user)
            ->count(25)
            ->create();

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson('/api/orders?per_page=10');
        $response->assertStatus(200)
            ->assertJsonPath('data.meta.per_page', 10)
            ->assertJsonPath('data.meta.total', 25);

        $this->assertCount(10, $response->json('data.data'));
    }

    /**
     * Test user can request specific page
     */
    public function test_user_can_request_specific_page(): void
    {
        Order::factory()
            ->for($this->user)
            ->count(25)
            ->create();

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson('/api/orders?per_page=10&page=2');

        $response->assertStatus(200)
            ->assertJsonPath('data.meta.current_page', 2);
    }
}
