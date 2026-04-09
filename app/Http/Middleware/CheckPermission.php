<?php

namespace App\Http\Middleware;

use App\Models\NavItem;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    protected static ?array $guestPrefixes = null;

    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // If the route is guest-viewable, allow everyone through
        if (str_ends_with($permission, '.view') && $this->isGuestViewable($permission)) {
            return $next($request);
        }

        if (!$request->user()) {
            return redirect()->guest(route('login'));
        }

        if (!$request->user()->hasPermission($permission)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }

    protected function isGuestViewable(string $permission): bool
    {
        if (static::$guestPrefixes === null) {
            static::$guestPrefixes = [];

            foreach (NavItem::tree() as $item) {
                if ($item->hasGuestAccess()) {
                    static::$guestPrefixes[] = trim($item->url, '/');
                }
                foreach ($item->children as $child) {
                    if ($child->hasGuestAccess()) {
                        static::$guestPrefixes[] = trim($child->url, '/');
                    }
                }
            }
        }

        $prefix = substr($permission, 0, -5); // strip '.view'

        return in_array($prefix, static::$guestPrefixes);
    }
}
