<?php

namespace App\Http\Middleware;

use Closure;

class CheckUserSuspended
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
        $user = $request->user();
        $now = date('Y-m-d H:i:s');

        if ($user->isSuspended()) {
            return redirect(route('suspended'));
        } else {
          $user->nullSuspended();
        }
        return $next($request);
    }
}
