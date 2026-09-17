@if(session()->has('impersonator_id'))
    <style>
        .impersonation-alert-strip {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(99, 102, 241, 0.15) 100%);
            border-bottom: 1px solid rgba(245, 158, 11, 0.35);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 9px 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
            transition: all 0.2s ease;
        }

        html[data-theme="dark"] .impersonation-alert-strip {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.2) 0%, rgba(99, 102, 241, 0.2) 100%);
            border-bottom-color: rgba(245, 158, 11, 0.4);
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.4);
        }

        .impersonation-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .impersonation-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            color: var(--text-primary);
        }

        .impersonation-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3px 10px;
            border-radius: 20px;
            background: rgba(245, 158, 11, 0.22);
            color: #f59e0b;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            border: 1px solid rgba(245, 158, 11, 0.45);
        }

        .impersonation-pulse {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #f59e0b;
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7);
            animation: pulse-amber 1.8s infinite;
        }

        @keyframes pulse-amber {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 7px rgba(245, 158, 11, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0);
            }
        }

        .impersonation-leave-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f59e0b;
            color: #0f172a;
            font-weight: 700;
            font-size: 12px;
            padding: 6px 14px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.35);
            font-family: inherit;
            white-space: nowrap;
        }

        .impersonation-leave-btn:hover {
            background: #d97706;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(245, 158, 11, 0.5);
        }

        .impersonation-leave-btn:active {
            transform: translateY(0);
        }
    </style>

    <div class="impersonation-alert-strip">
        <div class="impersonation-container">
            <div class="impersonation-meta">
                <span class="impersonation-badge">
                    <span class="impersonation-pulse"></span>
                    Mode Masuk Pengguna
                </span>
                <span>
                    Anda sedang melihat sistem sebagai:
                    <strong style="color:var(--text-primary); font-weight:700;">{{ Auth::user()->name }}</strong>
                    <span style="opacity:0.85; font-size:12px;">({{ Auth::user()->getRoleNames()->first() ?? 'User' }} &bull; {{ Auth::user()->email }})</span>
                </span>
            </div>
            <form action="{{ route('impersonate.leave') }}" method="POST" style="margin:0;">
                @csrf
                <button type="submit" class="impersonation-leave-btn" title="Keluar dari akun pengguna ini dan kembali ke akun Administrator Anda">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    <span>Kembali ke Akun Admin</span>
                </button>
            </form>
        </div>
    </div>
@endif
