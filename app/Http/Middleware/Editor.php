<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;

class Editor
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
        if ($request->user()->role !== User::ROLE_EDITOR) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
