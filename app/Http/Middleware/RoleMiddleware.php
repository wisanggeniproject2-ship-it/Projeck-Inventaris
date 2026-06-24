<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Debug - Log untuk cek
        \Log::info('Role Middleware - User: ' . $user->email . ', Role: ' . $user->role);
        \Log::info('Allowed roles: ' . implode(', ', $roles));
        
        // Cek apakah user memiliki role yang diizinkan
        if (!in_array($user->role, $roles)) {
            // Redirect ke dashboard yang sesuai
            return match($user->role) {
                'super_admin' => redirect()->route('super_admin.dashboard'),
                'admin_unit' => redirect()->route('admin_unit.dashboard'),
                'manager' => redirect()->route('manager.dashboard'),
                default => redirect()->route('user.dashboard'),
            };
        }

        return $next($request);
    }
}