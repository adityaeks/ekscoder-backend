<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VpsPinController extends Controller
{
    /**
     * Expected VPS PIN.
     */
    protected string $expectedPin = '8181';

    /**
     * Show the VPS PIN verification form.
     */
    public function showPinForm(Request $request): View|RedirectResponse
    {
        if ($request->session()->get('vps_pin_verified') === true) {
            return redirect()->route('admin.vps.index');
        }

        return view('admin.vps.pin-verify');
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
            $request->session()->put('vps_pin_verified', true);

            return redirect()->intended(route('admin.vps.index'))
                ->with('success', 'PIN Keamanan terverifikasi! Akses VPS Monitoring berhasil dibuka.');
        }

        return back()->withErrors([
            'pin' => 'PIN Keamanan salah! Silakan coba lagi.',
        ]);
    }

    /**
     * Lock VPS Monitoring access.
     */
    public function lockPin(Request $request): RedirectResponse
    {
        $request->session()->forget('vps_pin_verified');

        return redirect()->route('admin.vps-pin.show')
            ->with('success', 'Akses VPS Monitoring telah dikunci kembali.');
    }
}
