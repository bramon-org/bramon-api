<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Log;

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
        if (!in_array($request->user()->role, [User::ROLE_OPERATOR, User::ROLE_ADMIN])) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }

    public function terminate($request, $response)
    {
        Log::info('=== asdfsff dsaf ========');
    }
}
