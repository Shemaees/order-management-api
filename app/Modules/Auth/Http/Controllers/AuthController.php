<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\DTOs\LoginDTO;
use App\Modules\Auth\DTOs\RegisterDTO;
use App\Modules\Auth\Http\Requests\LoginRequest;
use App\Modules\Auth\Http\Requests\RegisterRequest;
use App\Modules\Auth\Http\Resources\UserResource;
use App\Modules\Auth\Services\AuthService;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function __construct(public AuthService $authService) {}

    /**
     * @throws \Throwable
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $dto = LoginDTO::fromRequest($request->validated());
            $result = $this->authService->login($dto);

            return $this->successResponse(
                data: $result,
                message: 'User logged in successfully',
            );
        } catch (Exception $exception) {
            return $this->errorResponse(
                $exception->getMessage(),
                $exception->getCode() ?: 401
            );
        }
    }

    /**
     * @throws \Throwable
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = RegisterDTO::fromRequest($request->validated());
        $result = $this->authService->register($dto);

        return $this->successResponse(
            $result,
            'User registered successfully',
            201,
        );
    }

    /**
     * @throws \Throwable
     */
    public function logout(): JsonResponse
    {
        $result = $this->authService->logout();
        if (! $result) {
            return $this->errorResponse('Logout failed', 500);
        }

        return $this->successResponse(
            null,
            'User logged out successfully',
            201,
        );
    }

    /**
     * @throws \Throwable
     */
    public function refresh(): JsonResponse
    {
        $token = $this->authService->refresh();

        return $this->successResponse(
            $token,
            'Token refreshed successfully',
        );
    }

    /**
     * @throws \Throwable
     */
    public function me(): JsonResponse
    {
        $user = $this->authService->me();

        return $this->successResponse(
            UserResource::make($user),
            'User Received Successfully.'
        );
    }
}
