<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminRoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $admin = Auth::guard('admin')->user();

        // if (!$admin || !in_array($admin->role, $roles)) {
        //     abort(403, 'Unauthorized');
        // }

        if (!$admin) {
            return redirect()->route('admin.login');
        }

        if (!in_array($admin->role, $roles)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
