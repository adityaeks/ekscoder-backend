<!-- Sidebar Overlay (Mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- Sidebar Navigation -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <a href="{{ route('dashboard') }}" class="logo-mark" style="display: flex; align-items: center; justify-content: center; width: 100%;">
            <img src="{{ asset('ekscoder.png') }}" alt="Ekscoder Logo" style="height: 38px; width: auto; max-width: 100%; object-fit: contain;">
        </a>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Main</div>

        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard">
            <div class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            </div>
            <span class="nav-text">Dashboard</span>
        </a>
        
        <!-- <div class="nav-section-label" style="margin-top:16px;">AI Chat</div> -->
        @can('ai_chat.view')
        <a href="{{ route('admin.ai-chat.index') }}" class="nav-item {{ request()->routeIs('admin.ai-chat.*') ? 'active' : '' }}" title="AI Chat (9Router)">
            <div class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"/><circle cx="12" cy="5" r="2"/><path d="M12 7v4"/><line x1="8" y1="16" x2="8.01" y2="16"/><line x1="16" y1="16" x2="16.01" y2="16"/></svg>
            </div>
            <span class="nav-text">AI Assistant</span>
        </a>
        <a href="{{ route('admin.ai-cs.index') }}" class="nav-item {{ request()->routeIs('admin.ai-cs.*') ? 'active' : '' }}" title="AI Customer Service (Landing Page Bot)">
            <div class="nav-icon" style="color:var(--amber, #f59e0b);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><circle cx="9" cy="10" r="1"/><circle cx="15" cy="10" r="1"/></svg>
            </div>
            <span class="nav-text">AI Customer Service</span>
        </a>
        @endcan
        <div class="nav-section-label" style="margin-top:16px;">Internal Management</div>
        @can('calendar.view')
        <a href="{{ route('admin.calendar.index') }}" class="nav-item {{ request()->routeIs('admin.calendar.*') ? 'active' : '' }}" title="Kalender & Agenda">
            <div class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <span class="nav-text">Kalender</span>
        </a>
        @endcan
            
        @can('orders.view')

        @php $activeOrderCount = \App\Models\ProjectOrder::whereNotIn('status', ['completed', 'cancelled'])->count(); @endphp

        <a href="{{ route('admin.orders.index') }}" class="nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" title="Project Orders">
            <div class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
            </div>
            <span class="nav-text">Project Orders</span>
            @php $badgeStyle = $activeOrderCount > 0 ? '' : 'display:none;'; @endphp
            <span class="nav-badge" id="sidebarOrdersBadge" style="{{ $badgeStyle }}">{{ $activeOrderCount }}</span>
        </a>
        @endcan

        @can('finance.view')
        <a href="{{ route('admin.finance.index') }}" class="nav-item {{ request()->routeIs('admin.finance.*') ? 'active' : '' }}" title="Keuangan & Kas">
            <div class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <span class="nav-text">Keuangan & Kas</span>
        </a>
        @endcan
        @can('notes.view')
        <a href="{{ route('admin.notes.index') }}" class="nav-item {{ request()->routeIs('admin.notes.*') ? 'active' : '' }}" title="Notes">
            <div class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15.5 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8.5L15.5 3z"/><path d="M14 3v5h5"/></svg>
            </div>
            <span class="nav-text">Notes</span>
        </a>
        @endcan

        @can('projects.view')
        <div class="nav-section-label" style="margin-top:16px;">Manage Web Porto & Content</div>

        <a href="{{ route('admin.projects.index') }}" class="nav-item {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}" title="Projects">
            <div class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
            </div>
            <span class="nav-text">Projects</span>
        </a>
        @endcan

        @can('posts.view')
        <a href="{{ route('admin.posts.index') }}" class="nav-item {{ request()->routeIs('admin.posts.*') || request()->routeIs('admin.blog-categories.*') ? 'active' : '' }}" title="Blog & Artikel">
            <div class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            </div>
            <span class="nav-text">Blog & Artikel</span>
        </a>
        @endcan

        
        <div class="nav-section-label" style="margin-top:16px;">Infrastructure</div>
        @can('sites.view')
        <a href="{{ route('admin.sites.index') }}" class="nav-item {{ request()->routeIs('admin.sites.*') ? 'active' : '' }}" title="Website Monitoring">
            <div class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            </div>
            <span class="nav-text">Website Monitoring</span>
        </a>
        @endcan
        <a href="{{ route('admin.vps.index') }}" class="nav-item {{ request()->routeIs('admin.vps.*') ? 'active' : '' }}" title="VPS Monitoring">
            <div class="nav-icon" style="color:#6366f1;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
            </div>
            <span class="nav-text">VPS Monitoring</span>
        </a>

        @can('cloudflare.view')
        <a href="{{ route('admin.cloudflare-zones.index') }}" class="nav-item {{ request()->routeIs('admin.cloudflare-zones.*') ? 'active' : '' }}" title="Cloudflare API">
            <div class="nav-icon" style="color:#f57c00;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/></svg>
            </div>
            <span class="nav-text">Cloudflare API</span>
        </a>
        @endcan


        <div class="nav-section-label" style="margin-top:16px;">System & Security</div>
        <a href="{{ route('profile.edit') }}" class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}" title="Profile">
            <div class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <span class="nav-text">Profile</span>
        </a>

        @can('users.view')
        <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" title="User Management">
            <div class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <span class="nav-text">User Management</span>
        </a>
        @endcan

        @can('roles.view')
        <a href="{{ route('admin.roles.index') }}" class="nav-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" title="Roles & Permissions">
            <div class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <span class="nav-text">Roles & Permissions</span>
        </a>
        @endcan

        @can('logs.view')
        <a href="{{ route('admin.logs.index') }}" class="nav-item {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}" title="User Activity Logs">
            <div class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </div>
            <span class="nav-text">Activity Logs</span>
        </a>
        @endcan

        <div class="nav-section-label" style="margin-top:16px;">Developer</div>

        <a href="/api/projects" target="_blank" class="nav-item" title="API Preview">
            <div class="nav-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
            </div>
            <span class="nav-text">API Preview</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar" title="{{ Auth::user()->name }}">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div class="user-info">
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-role">{{ Auth::user()->getRoleNames()->first() ?? 'User' }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn" title="Logout">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </button>
            </form>
        </div>
    </div>
</aside>

<script>
(function() {
    function initSidebarScroll() {
        const sidebarNav = document.querySelector('.sidebar-nav');
        if (!sidebarNav) return;

        const savedScroll = sessionStorage.getItem('ekscoder_sidebar_scroll');
        if (savedScroll !== null) {
            sidebarNav.scrollTop = parseInt(savedScroll, 10);
        }

        const activeItem = sidebarNav.querySelector('.nav-item.active');
        if (activeItem) {
            const navRect = sidebarNav.getBoundingClientRect();
            const itemRect = activeItem.getBoundingClientRect();
            if (itemRect.top < navRect.top || itemRect.bottom > navRect.bottom) {
                activeItem.scrollIntoView({ block: 'nearest', behavior: 'instant' });
            }
        }

        let scrollTimeout;
        sidebarNav.addEventListener('scroll', function() {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(function() {
                sessionStorage.setItem('ekscoder_sidebar_scroll', sidebarNav.scrollTop);
            }, 40);
        }, { passive: true });

        sidebarNav.addEventListener('click', function(e) {
            const link = e.target.closest('.nav-item');
            if (link) {
                sessionStorage.setItem('ekscoder_sidebar_scroll', sidebarNav.scrollTop);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSidebarScroll);
    } else {
        initSidebarScroll();
    }
})();
</script>
