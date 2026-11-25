<x-guest-layout>
    <div style="margin-bottom: 24px; text-align: center;">
        <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(255, 255, 255, 0.08); border: 1px solid #2A2A36; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 14px; color: #FFFFFF;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
        </div>
        <h1 class="auth-title">Enter Verification Code</h1>
        <p class="auth-subtitle" style="margin-bottom: 0;">
            We sent a 6-digit code to <strong style="color: #F4F4F6; font-family: 'JetBrains Mono', monospace;">{{ $maskedEmail }}</strong>.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if ($errors->any())
        <div style="margin-bottom: 18px; padding: 12px 14px; background: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.3); border-radius: 10px; color: #f43f5e; font-size: 13px; font-weight: 500;">
            @foreach ($errors->all() as $error)
                <div>• {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('otp.verify') }}" style="display: flex; flex-direction: column; gap: 20px;">
        @csrf

        <!-- 6-digit OTP Code Input -->
        <div>
            <label for="otp" style="display: block; font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 8px; text-align: center;">
                6-Digit OTP Code
            </label>
            
            <div style="position: relative;">
                <input id="otp" type="text" name="otp" required autofocus maxlength="6" pattern="[0-9]{6}" autocomplete="one-time-code"
                       placeholder="••••••"
                       style="width: 100%; padding: 14px 16px; background: #14141A; border: 1px solid #2A2A36; border-radius: 10px; color: #FFFFFF; font-family: 'JetBrains Mono', monospace; font-size: 24px; font-weight: 800; text-align: center; letter-spacing: 12px; outline: none; transition: border-color 0.2s, box-shadow 0.2s;"
                       onfocus="this.style.borderColor='#FFFFFF'; this.style.boxShadow='0 0 0 3px rgba(255, 255, 255, 0.14)';"
                       onblur="this.style.borderColor='#2A2A36'; this.style.boxShadow='none';"
                       oninput="this.value = this.value.replace(/[^0-9]/g, ''); if(this.value.length === 6) this.form.submit();">
            </div>
            <div style="font-size: 11.5px; color: #64748b; margin-top: 6px; text-align: center;">
                Code is valid for 10 minutes
            </div>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit" style="width: 100%; padding: 12px 20px; background: #FFFFFF; color: #08080A; border: none; border-radius: 8px; font-size: 13.5px; font-weight: 800; font-family: 'Inter', sans-serif; letter-spacing: -0.01em; cursor: pointer; transition: opacity 0.18s ease;"
                    onmouseover="this.style.opacity='0.9';"
                    onmouseout="this.style.opacity='1';">
                Verify & Continue
            </button>
        </div>
    </form>

    <!-- Resend & Cancel Actions -->
    <div style="margin-top: 22px; padding-top: 14px; border-top: 1px solid rgba(255, 255, 255, 0.06); display: flex; align-items: center; justify-content: space-between;">
        <form method="POST" action="{{ route('otp.resend') }}">
            @csrf
            <button type="submit" style="background: none; border: none; font-size: 12.5px; font-weight: 600; color: #FFFFFF; cursor: pointer; padding: 0; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.75'" onmouseout="this.style.opacity='1'">
                Didn't receive code? Resend Code
            </button>
        </form>

        <a href="{{ route('login') }}" style="font-size: 12.5px; font-weight: 500; color: #64748b; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#94a3b8'" onmouseout="this.style.color='#64748b'">
            &larr; Back to Sign In
        </a>
    </div>
</x-guest-layout>
