<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JwtMiddleware
{
    use ApiResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return $this->errorResponse(
                    'User not found',
                    404,
                );
            }
            return $next($request);
        } catch (TokenExpiredException $e) {
            return $this->errorResponse(
                'Token has expired',
                401
            );
        } catch (TokenInvalidException $e) {
            return $this->errorResponse(
                'Token is invalid',
                401
            );
        } catch (JWTException $e) {
            return $this->errorResponse(
                'Token not provided',
                401
            );
        }
    }
}
