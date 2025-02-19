<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Lang;
class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {   
        $user = auth()->user();

        // Check if user has an active subscription
        if ($user->role != 'admin' && !$user->hasActiveSubscription()) {
            return redirect()->route('subscription.plans')->with('error', Lang::get('subscription_required'));
        }

        // Validate event creation limit
        if ($user->role != 'admin' && !$user->canCreateEvent()) {
            return redirect()->route('events.index')->with('error', Lang::get('event_limit_exceeded'));
        }

        return $next($request);
    }
}
