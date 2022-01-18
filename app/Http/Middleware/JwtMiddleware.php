<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $controller = new Controller();
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (Exception | \Throwable $e) {
            if ($e instanceof TokenInvalidException) {
                return response()->json($controller->liteResponse(config('code.token.INVALID_TOKEN')));
            } elseif ($e instanceof TokenExpiredException) {
                return response()->json($controller->liteResponse(config('code.token.TOKEN_EXPIRED')));
            } elseif ($e instanceof TokenBlacklistedException) {
                return response()->json($controller->liteResponse(config('code.token.BLACK_LISTED_TOKEN')));
            } else {
                return response()->json($controller->liteResponse(config('code.token.NO_TOKEN')));
            }
        }
        return $next($request);
    }
}
