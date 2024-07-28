<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckVerified
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && !session('verfication_code')) {
            return $next($request);
        }

        return redirect()->route('verfication_code');
    }
}


