<x-admin-layout title="Cloudflare PIN Verification" breadcrumb="Infrastructure / Cloudflare / Security Lock">
    <div style="max-width: 460px; margin: 40px auto;">
        <div class="card" style="border: 1px solid rgba(245, 124, 0, 0.3); background: var(--bg-surface); padding: 32px 28px; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.5);">
            
            <!-- Header Lock Icon & Title -->
            <div style="text-align: center; margin-bottom: 24px;">
                <div style="width: 60px; height: 60px; border-radius: 18px; background: rgba(245, 124, 0, 0.15); border: 1px solid rgba(245, 124, 0, 0.3); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px; color: #f57c00;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                
                <h2 style="font-size: 20px; font-weight: 800; color: var(--text-primary); margin-bottom: 6px; letter-spacing: -0.4px;">
                    Cloudflare Protected Area 🔐
                </h2>
                <!-- <p style="font-size: 13px; color: var(--text-muted); margin: 0; line-height: 1.5;">
                    Masukkan <strong style="color: #f57c00;">PIN Keamanan 4-Digit</strong> untuk mengakses halaman kelola Cloudflare Zone & Domain.
                </p> -->
            </div>

            <!-- Error Messages -->
            @if($errors->any())
                <div style="margin-bottom: 20px; padding: 12px 14px; background: rgba(244, 63, 94, 0.12); border: 1px solid rgba(244, 63, 94, 0.3); border-radius: 10px; color: #f43f5e; font-size: 13px; font-weight: 600; text-align: center;">
                    @foreach($errors->all() as $error)
                        <div>• {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <!-- Flash Success / Status -->
            @if(session('success'))
                <div class="flash flash-success" style="margin-bottom: 20px; font-size: 13px;">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Form PIN -->
            <form method="POST" action="{{ route('admin.cloudflare-pin.verify') }}" style="display: flex; flex-direction: column; gap: 20px;">
                @csrf

                <div>
                    <!-- <label for="pin" style="display: block; font-size: 11.5px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; text-align: center;">
                        SECURITY PIN (4 DIGIT)
                    </label> -->

                    <div style="position: relative;">
                        <input id="pin" type="password" name="pin" required autofocus maxlength="6" pattern="[0-9]{4,6}" autocomplete="off"
                               placeholder="••••••"
                               style="width: 100%; padding: 16px; background: #16161f; border: 1px solid rgba(245, 124, 0, 0.4); border-radius: 14px; color: #f57c00; font-family: 'JetBrains Mono', monospace; font-size: 32px; font-weight: 900; text-align: center; letter-spacing: 14px; outline: none; transition: all 0.2s ease;"
                               onfocus="this.style.borderColor='#f57c00'; this.style.boxShadow='0 0 0 4px rgba(245, 124, 0, 0.2)';"
                               onblur="this.style.borderColor='rgba(245, 124, 0, 0.4)'; this.style.boxShadow='none';"
                               oninput="this.value = this.value.replace(/[^0-9]/g, ''); if(this.value.length === 6 || this.value === '8080') this.form.submit();">
                    </div>
                    <!-- <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 8px; text-align: center;">
                        PIN Default: <code style="color: #f57c00; font-family: 'JetBrains Mono', monospace; font-weight: 700;">8080</code>
                    </div> -->
                </div>

                <div>
                    <button type="submit" style="width: 100%; padding: 13px 20px; background: linear-gradient(135deg, #f57c00 0%, #ff9800 100%); color: #ffffff; border: none; border-radius: 12px; font-size: 14px; font-weight: 800; font-family: 'Inter', sans-serif; letter-spacing: 0.2px; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 0 20px rgba(245, 124, 0, 0.35);"
                            onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 0 28px rgba(245, 124, 0, 0.5)';"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 0 20px rgba(245, 124, 0, 0.35)';"
                            onmousedown="this.style.transform='translateY(1px)';">
                        Buka Akses Cloudflare &rarr;
                    </button>
                </div>
            </form>

            <!-- <div style="margin-top: 24px; text-align: center; border-top: 1px solid rgba(255, 255, 255, 0.06); padding-top: 16px;">
                <a href="{{ route('dashboard') }}" style="font-size: 12.5px; font-weight: 500; color: var(--text-muted); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--text-primary)'" onmouseout="this.style.color='var(--text-muted)'">
                    &larr; Kembali ke Dashboard
                </a>
            </div> -->

        </div>
    </div>
</x-admin-layout>
