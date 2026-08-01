<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CloudflarePinController extends Controller
{
    /**
     * Expected Cloudflare PIN.
     */
    protected string $expectedPin = '8080';

    /**
     * Show the Cloudflare PIN verification form.
     */
    public function showPinForm(Request $request): View|RedirectResponse
    {
        if ($request->session()->get('cloudflare_pin_verified') === true) {
            return redirect()->route('admin.cloudflare-zones.index');
        }

        return view('admin.cloudflare.pin-verify');
    }

    /**
     * Verify the entered PIN.
     */
    public function verifyPin(Request $request): RedirectResponse
    {
        $request->validate([
            'pin' => ['required', 'string', 'min:1', 'max:8'],
        ], [
            'pin.required' => 'Masukkan PIN keamanan.',
            'pin.min'      => 'PIN Keamanan salah! Silakan coba lagi.',
            'pin.max'      => 'PIN Keamanan salah! Silakan coba lagi.',
        ]);

        if (trim($request->pin) === $this->expectedPin) {
            $request->session()->put('cloudflare_pin_verified', true);

            return redirect()->intended(route('admin.cloudflare-zones.index'))
                ->with('success', 'PIN Keamanan terverifikasi! Akses Cloudflare berhasil dibuka.');
        }

        return back()->withErrors([
            'pin' => 'PIN Keamanan salah! Silakan coba lagi.',
        ]);
    }

    /**
     * Lock Cloudflare access.
     */
    public function lockPin(Request $request): RedirectResponse
    {
        $request->session()->forget('cloudflare_pin_verified');

        return redirect()->route('admin.cloudflare-pin.show')
            ->with('success', 'Akses Cloudflare Management telah dikunci kembali.');
    }
}
