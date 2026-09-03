<x-guest-layout>
    <div style="margin-bottom: 22px;">
        <h1 class="auth-title text-center">Welcome Back 👋</h1>
        <p class="auth-subtitle text-center">Sign in to access your Ekscoder Admin dashboard.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Error Alert Banner -->
    @if ($errors->any())
        <div style="background: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.28); border-radius: 12px; padding: 12px 14px; margin-bottom: 18px; display: flex; align-items: flex-start; gap: 10px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f43f5e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <div style="font-size: 12.5px; color: #fda4af; line-height: 1.45; font-weight: 500;">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

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
        </div>

        <!-- Submit Button -->
        <div style="margin-top: 6px;">
            <button type="submit" style="width: 100%; padding: 12px 20px; background: linear-gradient(135deg, #b8ff00 0%, #a0eb00 100%); color: #0a0a0f; border: none; border-radius: 10px; font-size: 14px; font-weight: 800; font-family: 'Inter', sans-serif; letter-spacing: -0.2px; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 0 20px rgba(184, 255, 0, 0.3);"
                    onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 0 28px rgba(184, 255, 0, 0.45)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 0 20px rgba(184, 255, 0, 0.3)';"
                    onmousedown="this.style.transform='translateY(1px)';">
                Sign In to Dashboard &rarr;
            </button>
        </div>
    </form>

    <!-- Divider -->
    <div style="display: flex; align-items: center; gap: 12px; margin: 20px 0 16px 0;">
        <div style="flex: 1; height: 1px; background: rgba(255, 255, 255, 0.08);"></div>
        <span style="font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">atau masuk dengan</span>
        <div style="flex: 1; height: 1px; background: rgba(255, 255, 255, 0.08);"></div>
    </div>

    <!-- Google Sign-In Button -->
    <div>
        <a href="{{ route('auth.google') }}"
           style="display: flex; align-items: center; justify-content: center; gap: 11px; width: 100%; padding: 11px 16px; background: #16161f; border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 10px; color: #f8fafc; font-size: 13.5px; font-weight: 600; text-decoration: none; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 2px 6px rgba(0,0,0,0.2);"
           onmouseover="this.style.background='#1e1e2c'; this.style.borderColor='rgba(255, 255, 255, 0.25)'; this.style.transform='translateY(-1px)';"
           onmouseout="this.style.background='#16161f'; this.style.borderColor='rgba(255, 255, 255, 0.12)'; this.style.transform='translateY(0)';"
           id="google-login-btn">
            <svg width="18" height="18" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17Z"/>
                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24Z"/>
                <path fill="#FBBC05" d="M5.28 14.27a7.22 7.22 0 0 1 0-4.54V6.58H1.25a11.98 11.98 0 0 0 0 10.84l4.03-3.15Z"/>
                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98Z"/>
            </svg>
            <span>Masuk dengan Google</span>
        </a>
    </div>

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
