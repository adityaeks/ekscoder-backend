<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyVpsPin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->get('vps_pin_verified') === true) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'VPS Monitoring Security PIN required',
                'redirect' => route('admin.vps-pin.show'),
            ], 403);
        }

        // Store current URL as intended destination before redirecting to PIN form
        return redirect()->guest(route('admin.vps-pin.show'));
    }
}
