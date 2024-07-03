<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestaurantMustBelongsToTheAuthenticatedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $restaurant = $request->route()->parameter('restaurant');

        if ($restaurant->user_id !== auth()->id()) {
            abort(401, 'You are not authorized');
        }
        return $next($request);
    }
}