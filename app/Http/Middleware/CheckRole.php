<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user is active
        if ($user->status !== 'active') {
            auth()->logout();
            return redirect()->route('login')->withErrors(['email' => 'Your account has been deactivated.']);
        }

        // Update last login
        $user->update(['last_login' => now()]);

        // Check if user has required role
        if (!empty($roles) && !in_array($user->role, $roles)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
