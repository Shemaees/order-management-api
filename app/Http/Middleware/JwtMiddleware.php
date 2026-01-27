<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     *
     * @throws AuthenticationException
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (! $user) {
                throw new NotFoundHttpException('User not found');
            }

            return $next($request);
        } catch (TokenExpiredException $e) {
            throw new AuthenticationException('Token has expired');
        } catch (TokenInvalidException $e) {
            throw new AuthenticationException('Token is invalid');
        } catch (JWTException $e) {
            throw new AuthenticationException('Token not provided');
        }
    }
}
