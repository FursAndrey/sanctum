<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HasBanChatMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $hasBanChat = (Auth::user()->banChat?->created_at) ? true : false;

        if ($hasBanChat === true) {
            return response(status: 423, content: json_encode(['message' => "You can't do this. You are blocked."]));
        }

        return $next($request);
    }
}
