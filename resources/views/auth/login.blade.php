<x-guest-layout>
    <div style="margin-bottom: 24px;">
        <h1 class="auth-title text-center">Welcome Back 👋</h1>
        <p class="auth-subtitle text-center">Sign in to access your Ekscoder Admin dashboard.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" style="display: flex; flex-direction: column; gap: 18px;">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" style="display: block; font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 7px;">
                Email Address
            </label>
            <div style="position: relative; display: flex; align-items: center;">
                <span style="position: absolute; left: 14px; color: #64748b; display: flex; align-items: center; pointer-events: none;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="4" width="20" height="16" rx="2"/>
                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                    </svg>
                </span>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                       placeholder="admin@ekscoder.com"
                       style="width: 100%; padding: 11px 14px 11px 42px; background: #16161f; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 10px; color: #f8fafc; font-size: 13.5px; outline: none; transition: border-color 0.2s, box-shadow 0.2s;"
                       onfocus="this.style.borderColor='#b8ff00'; this.style.boxShadow='0 0 0 3px rgba(184, 255, 0, 0.15)';"
                       onblur="this.style.borderColor='rgba(255, 255, 255, 0.08)'; this.style.boxShadow='none';">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 7px;">
                <label for="password" style="font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.6px;">
                    Password
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" style="font-size: 12px; font-weight: 600; color: #b8ff00; text-decoration: none; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                        Forgot password?
                    </a>
                @endif
            </div>
            <div style="position: relative; display: flex; align-items: center;">
                <span style="position: absolute; left: 14px; color: #64748b; display: flex; align-items: center; pointer-events: none;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </span>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       placeholder="••••••••"
                       style="width: 100%; padding: 11px 42px 11px 42px; background: #16161f; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 10px; color: #f8fafc; font-size: 13.5px; outline: none; transition: border-color 0.2s, box-shadow 0.2s;"
                       onfocus="this.style.borderColor='#b8ff00'; this.style.boxShadow='0 0 0 3px rgba(184, 255, 0, 0.15)';"
                       onblur="this.style.borderColor='rgba(255, 255, 255, 0.08)'; this.style.boxShadow='none';">

                <!-- Toggle Password Visibility Button -->
                <button type="button" onclick="togglePasswordVisibility()" style="position: absolute; right: 12px; background: none; border: none; color: #64748b; cursor: pointer; padding: 4px; display: flex; align-items: center; justify-content: center; transition: color 0.2s;" onmouseover="this.style.color='#f8fafc'" onmouseout="this.style.color='#64748b'" title="Toggle Password Visibility">
                    <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <!-- <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 4px;">
            <label for="remember_me" style="display: inline-flex; align-items: center; gap: 9px; cursor: pointer; user-select: none;">
                <input id="remember_me" type="checkbox" name="remember" style="width: 17px; height: 17px; accent-color: #b8ff00; border-radius: 4px; cursor: pointer;">
                <span style="font-size: 13px; font-weight: 500; color: #94a3b8;">Remember me for 30 days</span>
            </label>
        </div> -->

        <!-- Submit Button -->
        <div style="margin-top: 10px;">
            <button type="submit" style="width: 100%; padding: 12px 20px; background: linear-gradient(135deg, #b8ff00 0%, #a0eb00 100%); color: #0a0a0f; border: none; border-radius: 10px; font-size: 14px; font-weight: 800; font-family: 'Inter', sans-serif; letter-spacing: -0.2px; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 0 20px rgba(184, 255, 0, 0.3);"
                    onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 0 28px rgba(184, 255, 0, 0.45)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 0 20px rgba(184, 255, 0, 0.3)';"
                    onmousedown="this.style.transform='translateY(1px)';">
                Sign In to Dashboard &rarr;
            </button>
        </div>
    </form>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>';
            }
        }
    </script>
</x-guest-layout>
