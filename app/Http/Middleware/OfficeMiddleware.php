<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class OfficeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = auth('office-api')->user();

            // التحقق إن المستخدم هو من نوع Office
            if (!$user instanceof \App\Models\Office) {
                return response()->json(['error' => 'Unauthorized. Offices only.'], 403);
            }

            return $next($request);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unauthorized or invalid token.'], 401);
        }
    }
}
