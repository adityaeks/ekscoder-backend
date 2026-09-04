<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect user to Google OAuth consent screen.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        if (empty(config('services.google.client_id')) || empty(config('services.google.client_secret'))) {
            return redirect()->route('login')->withErrors([
                'email' => 'Fitur login Google belum aktif karena kredensial (GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET) belum diatur di .env.',
            ]);
        }

        try {
            return Socialite::driver('google')->redirect();
        } catch (\Throwable $e) {
            Log::error('Google Auth Redirect Error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors([
                'email' => 'Tidak dapat terhubung ke server Google saat ini. Silakan coba lagi nanti.',
            ]);
        }
    }

    /**
     * Handle callback from Google OAuth.
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        // Handle user cancellation or denial
        if ($request->has('error')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Proses masuk dengan Google dibatalkan.',
            ]);
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::error('Google Auth Callback Error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors([
                'email' => 'Gagal mengambil informasi profil dari Google. Silakan coba kembali.',
            ]);
        }

        $email = $googleUser->getEmail();

        if (empty($email)) {
            return redirect()->route('login')->withErrors([
                'email' => 'Akun Google Anda tidak memiliki alamat email yang valid.',
            ]);
        }

        // Cari pengguna di database berdasarkan email
        $user = User::where('email', $email)->first();

        // KETENTUAN UTAMA: Hanya akun yang telah didaftarkan yang boleh masuk
        if (!$user) {
            Log::warning("Percobaan login Google tidak sah dengan email belum terdaftar: {$email}");

            return redirect()->route('login')->withErrors([
                'email' => "Akun Google ({$email}) belum terdaftar dalam sistem. Hanya pengguna yang telah didaftarkan oleh administrator yang dapat masuk.",
            ]);
        }

        // Tautkan Google ID dan update avatar jika tersedia
        $user->forceFill([
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar() ?: $user->avatar,
            'email_verified_at' => $user->email_verified_at ?: now(),
        ])->save();

        // Login pengguna ke dalam sistem
        Auth::login($user, false);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
