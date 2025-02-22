<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $roles = Auth::user()->roles->pluck('title')->toArray();

        if (! in_array('Admin', $roles)) {
            return response(status: 403, content: json_encode(['message' => 'This action is unauthorized.']));
        }

        return $next($request);
    }
}
