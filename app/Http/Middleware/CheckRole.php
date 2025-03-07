<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user()) {
            return response()->json([
                'message' => 'Yetkilendirme hatası.',
            ], 401);
        }
        
        $user = $request->user();
        
        if ($user->isSuperAdmin()) {
            return $next($request);
        }
        
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }
        
        return response()->json([
            'message' => 'Bu işlem için yetkiniz bulunmamaktadır.',
        ], 403);
    }
}