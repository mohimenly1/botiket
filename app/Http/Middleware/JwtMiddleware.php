<?php

namespace App\Http\Middleware;

use App\Http\Traits\ResponseTraits;
use Closure;
use Illuminate\Http\Request;
use JWTAuth;

class JwtMiddleware
{
    use ResponseTraits;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return $this->prepare_response( __('auth.Something went wrong'), __('auth.failed'), null, 401);
            }
            return $next($request);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->prepare_response( 'token_expired', __('auth.failed'), null, 401);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->prepare_response( 'token_invalid', __('auth.failed'), null, 401);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->prepare_response( 'token_required', __('auth.failed'), null, 401);

        }
        return $next($request);

    }
    
}
