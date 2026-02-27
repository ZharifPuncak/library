<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Kalau user login dan role dia admin
        if (Auth::check() && Auth::user()->isAdmin()) {
            return $next($request); // bagi lepas
        }

        // Kalau bukan admin, redirect ke home dengan mesej error
        return redirect('/')->with('error', 'You do not have admin access.');
    }
}
