<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $user = User::factory()->make()->toArray();
        $user['password'] = 'password';
        $response = $this->postJson('/api/auth/register', $user);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                    ],
                    'access' => [
                        'token',
                        'type',
                        'expired_at',
                    ],
                ],
            ])->assertJson([
                'status' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => [
                        'name' => $user['name'],
                        'email' => $user['email'],
                    ],
                    'access' => [
                        'type' => 'Bearer',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $user['email'],
            'name' => $user['name'],
        ]);
    }

    public function test_user_registeration_validation_fails()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 1234,
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'errors',
            ])->assertJson([
                'status' => false,
            ]);
    }

    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'password' => 'P@ssw0rd123',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'P@ssw0rd123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                    ],
                    'access' => [
                        'token',
                        'type',
                        'expired_at',
                    ],
                ],
            ])->assertJson([
                'status' => true,
                'message' => 'User logged in successfully',
            ]);

    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'status' => false,
                'message' => 'Invalid credentials',
            ]);
    }

    public function test_authenticated_user_can_logout()
    {
        $user = User::factory()->create();
        /** @phpstan-ignore-next-line */
        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('api/auth/logout');

        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
                'message' => 'User logged out successfully',
            ]);
    }

    public function test_authenticated_user_can_refresh_token()
    {
        $user = User::factory()->create();
        /** @phpstan-ignore-next-line */
        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('api/auth/refresh');
        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Token refreshed successfully',
                'data' => [
                    'access' => [
                        'type' => 'Bearer',
                    ],
                ],
            ]);
    }

    public function test_authenticated_user_can_get_his_profile()
    {
        $user = User::factory()->create();
        /** @phpstan-ignore-next-line */
        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('api/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'User Received Successfully.',
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
    }
}
