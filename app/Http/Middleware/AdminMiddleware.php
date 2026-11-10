<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Registered after 'auth' on every /admin route (see bootstrap/app.php and
 * routes/web.php): 'auth' already guarantees a logged-in user by the time
 * this runs, so an unauthenticated guest never reaches here at all (they're
 * redirected to /login first, same as any other auth-protected page). This
 * middleware only has to decide one thing — is the authenticated user an
 * admin — and does that from the database (Auth::user()->role), never from
 * anything the client could influence.
 */
class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check() || ! Auth::user()->isAdmin()) {
            abort(403, 'You do not have access to the admin area.');
        }

        return $next($request);
    }
}
