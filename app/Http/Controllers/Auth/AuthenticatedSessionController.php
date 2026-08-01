<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Notifications\SendLoginOtp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request by sending an OTP.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $user = $request->validateCredentials();

        // Generate 6-digit OTP code
        $otp = sprintf('%06d', random_int(0, 999999));

        // Store OTP details in session
        session([
            'auth.otp' => [
                'user_id'    => $user->id,
                'code'       => $otp,
                'expires_at' => now()->addMinutes(10)->timestamp,
                'remember'   => $request->boolean('remember'),
            ]
        ]);

        // Send OTP via email notification
        try {
            $user->notify(new SendLoginOtp($otp));
        } catch (\Throwable $e) {
            Log::error("Failed sending login OTP to {$user->email}: " . $e->getMessage());
        }

        return redirect()->route('otp.verify')->with('status', 'A 6-digit verification code has been sent to your email address.');
    }

    /**
     * Display the OTP verification view.
     */
    public function showOtp(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('auth.otp')) {
            return redirect()->route('login');
        }

        $otpData = $request->session()->get('auth.otp');
        $user = User::find($otpData['user_id']);

        if (!$user) {
            $request->session()->forget('auth.otp');
            return redirect()->route('login');
        }

        // Mask email: e.g. ad***a@gmail.com
        $email = $user->email;
        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1] ?? '';
        $maskedName = strlen($name) <= 2
            ? substr($name, 0, 1) . '***'
            : substr($name, 0, 2) . str_repeat('*', max(1, strlen($name) - 3)) . substr($name, -1);
        $maskedEmail = $maskedName . '@' . $domain;

        return view('auth.otp-verify', compact('maskedEmail'));
    }

    /**
     * Verify the submitted OTP code.
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        if (!$request->session()->has('auth.otp')) {
            return redirect()->route('login')->withErrors(['email' => 'Session expired. Please log in again.']);
        }

        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'otp.required' => 'Please enter the 6-digit verification code.',
            'otp.size' => 'The verification code must be exactly 6 digits.',
        ]);

        $otpData = $request->session()->get('auth.otp');

        // Check expiration (10 minutes)
        if (now()->timestamp > $otpData['expires_at']) {
            return back()->withErrors(['otp' => 'The verification code has expired. Please click resend to get a new code.']);
        }

        // Check code match
        if (trim($request->otp) !== $otpData['code']) {
            return back()->withErrors(['otp' => 'Invalid verification code. Please check your email and try again.']);
        }

        // Complete authentication
        $user = User::findOrFail($otpData['user_id']);
        Auth::login($user, $otpData['remember'] ?? false);

        $request->session()->forget('auth.otp');
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Resend a fresh OTP code.
     */
    public function resendOtp(Request $request): RedirectResponse
    {
        if (!$request->session()->has('auth.otp')) {
            return redirect()->route('login')->withErrors(['email' => 'Session expired. Please log in again.']);
        }

        $otpData = $request->session()->get('auth.otp');
        $user = User::find($otpData['user_id']);

        if (!$user) {
            $request->session()->forget('auth.otp');
            return redirect()->route('login');
        }

        // Generate new 6-digit OTP code
        $newOtp = sprintf('%06d', random_int(0, 999999));

        $otpData['code'] = $newOtp;
        $otpData['expires_at'] = now()->addMinutes(10)->timestamp;

        $request->session()->put('auth.otp', $otpData);

        try {
            $user->notify(new SendLoginOtp($newOtp));
        } catch (\Throwable $e) {
            Log::error("Failed resending login OTP to {$user->email}: " . $e->getMessage());
        }

        return redirect()->route('otp.verify')->with('status', 'A new verification code has been sent to your email address.');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
