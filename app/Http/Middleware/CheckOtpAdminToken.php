<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;

class CheckOtpAdminToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $otpToken = $request->header('X-Otp-Token');

        if (!$otpToken || !Cache::has('otp_admin_token_' . $otpToken)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa xác thực OTP hoặc mã đã hết hạn.'
            ], 401);
        }

        return $next($request);
    }
}
