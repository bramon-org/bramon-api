<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;

class Operator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()->role !== User::ROLE_OPERATOR) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
