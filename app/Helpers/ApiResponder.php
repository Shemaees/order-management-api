<?php

namespace App\Helpers;

use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class ApiResponder
{
    use ApiResponseTrait;

    public static function error(string $message, int $errorCode, array $errors = []): JsonResponse
    {
        return (new self)->errorResponse(
            $message,
            $errorCode,
            $errors
        );
    }
}
