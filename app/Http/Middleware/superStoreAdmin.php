<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\ResponseTraits;

class superStoreAdmin
{
    use ResponseTraits;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user()->role != 'super-admin' &&  Auth::user()->role != 'store-admin') {
            return $this->prepare_response(__('auth.Something went wrong'), __('auth.not allowed'), null, 401);
        }
    
        return $next($request);
    }
}
