<?php

namespace App\Modules\Auth\Services;

use App\Base\BaseService;
use App\Modules\Auth\DTOs\LoginDTO;
use App\Modules\Auth\DTOs\RegisterDTO;
use App\Modules\Auth\Http\Resources\UserResource;
use App\Modules\Auth\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class AuthService extends BaseService
{
    public function __construct(public UserRepository $userRepository) {}

    /**
     * @return array|void
     *
     * @throws \Throwable
     */
    public function register(RegisterDTO $DTO)
    {
        try {
            $DTO->password = Hash::make($DTO->password);
            $user = $this->userRepository->create($DTO->toArray());
            $token = auth('api')->attempt([
                'email' => $DTO->email,
                'password' => $DTO->password,
            ]);

            return [
                'user' => UserResource::make($user),
                'access' => [
                    'token' => $token,
                    'type' => 'Bearer',
                    'expired_at' => now()->addMinutes((int) config('jwt.ttl'))->toISOString(),
                ],
            ];
        } catch (\Throwable $exception) {
            $this->handleException($exception);
        }
    }

    /**
     * @return void|array
     *
     * @throws \Throwable
     */
    public function login(LoginDTO $DTO)
    {
        try {
            if (
                ! $token = auth('api')->attempt([
                    'email' => $DTO->email,
                    'password' => $DTO->password,
                ])
            ) {
                throw new \RuntimeException('Invalid credentials', 401);
            }

            $user = auth('api')->user();

            return [
                'user' => UserResource::make($user),
                'access' => [
                    'token' => $token,
                    'type' => 'Bearer',
                    'expired_at' => now()->addMinutes((int) config('jwt.ttl'))->toISOString(),
                ],
            ];
        } catch (\Throwable $exception) {
            $this->handleException($exception);
        }
    }

    /**
     * @return UserResource|void
     *
     * @throws \Throwable
     */
    public function me()
    {
        try {
            $user = auth()->user();

            return UserResource::make($user);
        } catch (\Throwable $exception) {
            $this->handleException($exception);
        }
    }

    /**
     * @return array[]|void
     *
     * @throws \Throwable
     */
    public function refresh()
    {
        try {
            /** @phpstan-ignore-next-line */
            $newToken = auth('api')->refresh();

            return [
                'access' => [
                    'token' => $newToken,
                    'type' => 'Bearer',
                    'expired_at' => now()->addMinutes((int) config('jwt.ttl'))->toISOString(),
                ],
            ];
        } catch (\Throwable $exception) {
            $this->handleException($exception);
        }
    }

    /**
     * @return true|void
     *
     * @throws \Throwable
     */
    public function logout()
    {
        try {
            auth('api')->logout();

            return true;
        } catch (\Throwable $exception) {
            $this->handleException($exception);
        }
    }
}
