<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UpdateLastSeen
{
    /**
     * Stamps last_seen_at for "who's active now" dashboard panels. Throttled
     * to once a minute per user so this doesn't add a write to every request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && (! $user->last_seen_at || $user->last_seen_at->lt(now()->subMinute()))) {
            $user->timestamps = false;
            $user->forceFill(['last_seen_at' => now()])->save();
        }

        return $next($request);
    }
}
