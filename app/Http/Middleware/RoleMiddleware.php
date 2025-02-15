<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            // Jika pengguna tidak ditemukan
            if (!$user) {
                return response()->json(['error' => 'Unauthorized - User Not Found'], 403);
            }

            // Cek apakah role cocok
            $roles = explode('|', $role); // Memungkinkan banyak role seperti "admin|superadmin"
            if (!in_array($user->role, $roles)) {
                return response()->json(['error' => 'Unauthorized - Access Denied'], 403);
            }

            return $next($request);
        } catch (Exception $e) {
            return response()->json(['error' => 'Unauthorized - Invalid Token'], 401);
        }
    }
}
