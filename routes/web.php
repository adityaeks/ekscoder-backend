<?php

use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\NoteController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\AiChatController;
use App\Http\Controllers\Admin\AiCsAdminController;
use App\Http\Controllers\Admin\EModulController;

use App\Http\Controllers\Admin\VpsPinController;
use App\Http\Controllers\Admin\VpsServerController;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use App\Models\Project;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');

Route::post('/', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');


use App\Models\ProjectOrder;
use App\Models\UserLog;

Route::get('/dashboard', function () {
    $user = auth()->user();
    $isAdmin = $user && $user->can('dashboard.admin');

    if (!$isAdmin) {
        return view('dashboard', [
            'isAdmin' => false,
            'stats' => [
                'total_projects'   => 0,
                'active_projects'  => 0,
                'featured_projects'=> 0,
                'total_pipeline'   => 0,
                'total_paid'       => 0,
                'active_orders'    => 0,
                'completed_orders' => 0,
            ],
            'projects' => collect(),
            'recentOrders' => collect(),
            'recentLogs' => collect(),
        ]);
    }

    $projects = Project::orderBy('order', 'asc')->get();
    $allOrders = ProjectOrder::latest()->get();

    $stats = [
        'total_projects'   => $projects->count(),
        'active_projects'  => $projects->where('is_active', true)->count(),
        'featured_projects'=> $projects->where('featured', true)->count(),
        'total_pipeline'   => $allOrders->where('status', '!=', 'cancelled')->sum('budget'),
        'total_paid'       => $allOrders->where('status', '!=', 'cancelled')->sum('paid_amount'),
        'active_orders'    => $allOrders->whereNotIn('status', ['completed', 'cancelled'])->count(),
        'completed_orders' => $allOrders->where('status', 'completed')->count(),
    ];

    $recentOrders = $allOrders->take(5);
    $recentLogs   = UserLog::latest()->take(6)->get();

    return view('dashboard', compact('stats', 'projects', 'recentOrders', 'recentLogs') + ['isAdmin' => true]);
})->middleware(['auth', 'verified'])->name('dashboard');

use App\Http\Controllers\Admin\ProjectOrderController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\MonitoredSiteController;
use App\Http\Controllers\Admin\CloudflareZoneController;
use App\Http\Controllers\Admin\CloudflareDnsController;
use App\Http\Controllers\Admin\CloudflarePinController;
use App\Http\Controllers\Admin\FinancialController;

Route::middleware('auth')->group(function () {
    Route::post('/impersonate/leave', [UserController::class, 'leaveImpersonation'])->name('impersonate.leave');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        // Projects Management
        Route::patch('projects/{project}/toggle-active', [ProjectController::class, 'toggleActive'])
            ->name('projects.toggle-active')
            ->middleware('can:projects.toggle-active');

        Route::resource('projects', ProjectController::class)->middleware([
            'index'   => 'can:projects.view',
            'show'    => 'can:projects.view',
            'create'  => 'can:projects.create',
            'store'   => 'can:projects.create',
            'edit'    => 'can:projects.edit',
            'update'  => 'can:projects.edit',
            'destroy' => 'can:projects.delete',
        ]);

        // Project Orders Kanban Board
        Route::patch('orders/{order}/update-status', [ProjectOrderController::class, 'updateStatus'])
            ->name('orders.update-status')
            ->middleware('can:orders.update-status');

        Route::resource('orders', ProjectOrderController::class)->middleware([
            'index'   => 'can:orders.view',
            'show'    => 'can:orders.view',
            'create'  => 'can:orders.create',
            'store'   => 'can:orders.create',
            'edit'    => 'can:orders.edit',
            'update'  => 'can:orders.edit',
            'destroy' => 'can:orders.delete',
        ]);

        // User Activity Logs
        Route::get('logs', [ActivityLogController::class, 'index'])
            ->name('logs.index')
            ->middleware('can:logs.view');

        Route::delete('logs/clear', [ActivityLogController::class, 'clear'])
            ->name('logs.clear')
            ->middleware('can:logs.clear');

        // User Access Management
        Route::post('users/{user}/impersonate', [UserController::class, 'impersonate'])
            ->name('users.impersonate')
            ->middleware('can:users.edit');

        Route::resource('users', UserController::class)->middleware([
            'index'   => 'can:users.view',
            'show'    => 'can:users.view',
            'create'  => 'can:users.create',
            'store'   => 'can:users.create',
            'edit'    => 'can:users.edit',
            'update'  => 'can:users.edit',
            'destroy' => 'can:users.delete',
        ]);

        Route::post('permissions', [RoleController::class, 'storePermission'])
            ->name('permissions.store')
            ->middleware('can:roles.create');

        Route::delete('permissions/{permission}', [RoleController::class, 'destroyPermission'])
            ->name('permissions.destroy')
            ->middleware('can:roles.delete');

        Route::resource('roles', RoleController::class)->middleware([
            'index'   => 'can:roles.view',
            'show'    => 'can:roles.view',
            'create'  => 'can:roles.create',
            'store'   => 'can:roles.create',
            'edit'    => 'can:roles.edit',
            'update'  => 'can:roles.edit',
            'destroy' => 'can:roles.delete',
        ]);

        // Website Health & Uptime Monitoring
        Route::post('sites/{site}/check', [MonitoredSiteController::class, 'check'])
            ->name('sites.check')
            ->middleware('can:sites.check');

        Route::resource('sites', MonitoredSiteController::class)->middleware([
            'index'   => 'can:sites.view',
            'show'    => 'can:sites.view',
            'create'  => 'can:sites.create',
            'store'   => 'can:sites.create',
            'edit'    => 'can:sites.edit',
            'update'  => 'can:sites.edit',
            'destroy' => 'can:sites.delete',
        ]);

        // VPS PIN Security Routes
        Route::get('vps-pin', [VpsPinController::class, 'showPinForm'])
            ->name('vps-pin.show')
            ->middleware('can:vps.view');

        Route::post('vps-pin', [VpsPinController::class, 'verifyPin'])
            ->name('vps-pin.verify')
            ->middleware('can:vps.view');

        Route::post('vps-pin/lock', [VpsPinController::class, 'lockPin'])
            ->name('vps-pin.lock')
            ->middleware('can:vps.view');

        // VPS Server Monitoring (Protected by PIN 8181)
        Route::middleware(['vps.pin', 'can:vps.view'])->group(function () {
            Route::resource('vps', VpsServerController::class)->parameters([
                'vps' => 'vps'
            ])->middleware([
                'index'   => 'can:vps.view',
                'show'    => 'can:vps.view',
                'create'  => 'can:vps.create',
                'store'   => 'can:vps.create',
                'edit'    => 'can:vps.edit',
                'update'  => 'can:vps.edit',
                'destroy' => 'can:vps.delete',
            ]);
        });




        // Cloudflare PIN Security Routes
        Route::get('cloudflare-pin', [CloudflarePinController::class, 'showPinForm'])
            ->name('cloudflare-pin.show')
            ->middleware('can:cloudflare.view');

        Route::post('cloudflare-pin', [CloudflarePinController::class, 'verifyPin'])
            ->name('cloudflare-pin.verify')
            ->middleware('can:cloudflare.view');

        Route::post('cloudflare-pin/lock', [CloudflarePinController::class, 'lockPin'])
            ->name('cloudflare-pin.lock')
            ->middleware('can:cloudflare.view');

        // Cloudflare Integration Routes (Protected by PIN)
        Route::middleware('cloudflare.pin')->group(function () {
            Route::resource('cloudflare-zones', CloudflareZoneController::class)
                ->only(['index', 'store', 'show', 'destroy'])
                ->middleware([
                    'index'   => 'can:cloudflare.view',
                    'show'    => 'can:cloudflare.view',
                    'store'   => 'can:cloudflare.create',
                    'destroy' => 'can:cloudflare.delete',
                ]);

            Route::post('cloudflare-zones/{zone}/purge-cache', [CloudflareZoneController::class, 'purgeCache'])
                ->name('cloudflare-zones.purge-cache')
                ->middleware('can:cloudflare.purge');

            Route::post('cloudflare-zones/{zone}/security', [CloudflareZoneController::class, 'updateSecurity'])
                ->name('cloudflare-zones.update-security')
                ->middleware('can:cloudflare.edit');

            Route::post('cloudflare-zones/{zone}/dns', [CloudflareDnsController::class, 'store'])
                ->name('cloudflare-dns.store')
                ->middleware('can:cloudflare.edit');

            Route::put('cloudflare-zones/{zone}/dns/{record}', [CloudflareDnsController::class, 'update'])
                ->name('cloudflare-dns.update')
                ->middleware('can:cloudflare.edit');

            Route::patch('cloudflare-zones/{zone}/dns/{record}/proxy', [CloudflareDnsController::class, 'toggleProxy'])
                ->name('cloudflare-dns.toggle-proxy')
                ->middleware('can:cloudflare.edit');

            Route::delete('cloudflare-zones/{zone}/dns/{record}', [CloudflareDnsController::class, 'destroy'])
                ->name('cloudflare-dns.destroy')
                ->middleware('can:cloudflare.edit');
        });

        // Financial Management Routes
        Route::get('finance', [FinancialController::class, 'index'])
            ->name('finance.index')
            ->middleware('can:finance.view');

        Route::post('finance', [FinancialController::class, 'store'])
            ->name('finance.store')
            ->middleware('can:finance.manage');

        Route::delete('finance/{transaction}', [FinancialController::class, 'destroy'])
            ->name('finance.destroy')
            ->middleware('can:finance.manage');

        Route::post('finance/categories', [FinancialController::class, 'storeCategory'])
            ->name('finance.categories.store')
            ->middleware('can:finance.manage');

        Route::delete('finance/categories/{category}', [FinancialController::class, 'destroyCategory'])
            ->name('finance.categories.destroy')
            ->middleware('can:finance.manage');

        // Google Keep Style Notes Routes
        Route::resource('notes', NoteController::class)
            ->middleware([
                'index'   => 'can:notes.view',
                'show'    => 'can:notes.view',
                'store'   => 'can:notes.create',
                'update'  => 'can:notes.edit',
                'destroy' => 'can:notes.delete',
            ]);
        Route::post('notes/reorder', [NoteController::class, 'reorder'])->name('notes.reorder')->middleware('can:notes.edit');
        Route::post('notes/{note}/pin', [NoteController::class, 'togglePin'])->name('notes.pin')->middleware('can:notes.edit');
        Route::patch('notes/{note}/color', [NoteController::class, 'updateColor'])->name('notes.color')->middleware('can:notes.edit');

        // Calendar & Agenda Routes
        Route::resource('calendar', CalendarController::class)
            ->middleware([
                'index'   => 'can:calendar.view',
                'show'    => 'can:calendar.view',
                'store'   => 'can:calendar.create',
                'update'  => 'can:calendar.edit',
                'destroy' => 'can:calendar.delete',
            ]);
        Route::patch('calendar/{calendar}/toggle-complete', [CalendarController::class, 'toggleComplete'])
            ->name('calendar.toggle-complete')
            ->middleware('can:calendar.edit');

        // Blog Posts & Categories Routes
        Route::post('posts/generate-ai', [BlogPostController::class, 'generateAiArticle'])
            ->name('posts.generate-ai')
            ->middleware('can:posts.create');

        Route::patch('posts/{post}/toggle-publish', [BlogPostController::class, 'togglePublish'])
            ->name('posts.toggle-publish')
            ->middleware('can:posts.publish');

        Route::patch('posts/{post}/toggle-featured', [BlogPostController::class, 'toggleFeatured'])
            ->name('posts.toggle-featured')
            ->middleware('can:posts.edit');

        Route::resource('posts', BlogPostController::class)->middleware([
            'index'   => 'can:posts.view',
            'show'    => 'can:posts.view',
            'create'  => 'can:posts.create',
            'store'   => 'can:posts.create',
            'edit'    => 'can:posts.edit',
            'update'  => 'can:posts.edit',
            'destroy' => 'can:posts.delete',
        ]);

        Route::resource('blog-categories', BlogCategoryController::class)->except(['create', 'show', 'edit'])->middleware([
            'index'   => 'can:posts.view',
            'store'   => 'can:posts.create',
            'update'  => 'can:posts.edit',
            'destroy' => 'can:posts.delete',
        ]);

        // AI Chat (9Router) Routes
        Route::prefix('ai-chat')->name('ai-chat.')->middleware('can:ai_chat.view')->group(function () {
            Route::get('/', [AiChatController::class, 'index'])->name('index');
            Route::get('/models', [AiChatController::class, 'getModels'])->name('models');
            Route::get('/conversations', [AiChatController::class, 'getConversations'])->name('conversations.index');
            Route::post('/conversations', [AiChatController::class, 'storeConversation'])->name('conversations.store');
            Route::put('/conversations/{id}', [AiChatController::class, 'updateConversation'])->name('conversations.update');
            Route::delete('/conversations/{id}', [AiChatController::class, 'destroyConversation'])->name('conversations.destroy');
            Route::get('/conversations/{id}/messages', [AiChatController::class, 'getMessages'])->name('messages.index');
            Route::delete('/conversations/{id}/messages', [AiChatController::class, 'clearMessages'])->name('messages.clear');
            Route::post('/send', [AiChatController::class, 'sendMessage'])->name('send');
            Route::post('/settings', [AiChatController::class, 'saveSettings'])->middleware('can:ai_chat.settings')->name('settings.save');
            Route::post('/test-connection', [AiChatController::class, 'testConnection'])->name('test-connection');
        });

        // AI Customer Service (Landing Page Bot Logs & Prompt Settings)
        Route::prefix('ai-cs')->name('ai-cs.')->middleware('can:ai_cs.view')->group(function () {
            Route::get('/', [AiCsAdminController::class, 'index'])->name('index');
            Route::get('/sessions/{sessionId}/messages', [AiCsAdminController::class, 'getSessionMessages'])->name('sessions.messages');
            Route::delete('/sessions/{sessionId}', [AiCsAdminController::class, 'destroySession'])->middleware('can:ai_cs.manage')->name('sessions.destroy');
            Route::post('/clear-logs', [AiCsAdminController::class, 'clearLogs'])->middleware('can:ai_cs.manage')->name('clear-logs');
            Route::post('/settings', [AiCsAdminController::class, 'saveSettings'])->middleware('can:ai_cs.manage')->name('settings.save');
            Route::post('/test-chat', [AiCsAdminController::class, 'testChat'])->name('test-chat');
        });

        // E-Modul Management Routes
        Route::get('e-modul/ai-settings', [EModulController::class, 'settings'])
            ->middleware('can:emodul.edit')
            ->name('e-modul.settings');
        Route::get('e-modul-settings', [EModulController::class, 'getAiSettings'])
            ->middleware('can:emodul.edit')
            ->name('e-modul.settings.get');
        Route::post('e-modul-settings', [EModulController::class, 'saveAiSettings'])
            ->middleware('can:emodul.edit')
            ->name('e-modul.settings.save');
        Route::post('e-modul/test-prompt', [EModulController::class, 'testPrompt'])
            ->middleware('can:emodul.edit')
            ->name('e-modul.test-prompt');

        Route::post('e-modul/ask-ai', [EModulController::class, 'askAi'])->name('e-modul.ask-ai-general');
        Route::post('e-modul/{e_modul}/ask-ai', [EModulController::class, 'askAi'])->name('e-modul.ask-ai');
        Route::patch('e-modul/{e_modul}/toggle-active', [EModulController::class, 'toggleActive'])
            ->middleware('can:emodul.toggle-active')
            ->name('e-modul.toggle-active');

        // E-Modul Server Cache Routes (WebP Page Cache)
        Route::get('e-modul/{e_modul}/cache-status', [EModulController::class, 'getCacheStatus'])->name('e-modul.cache-status');
        Route::post('e-modul/{e_modul}/save-cache-batch', [EModulController::class, 'saveCacheBatch'])->name('e-modul.save-cache-batch');
        Route::post('e-modul/{e_modul}/clear-cache', [EModulController::class, 'clearCache'])
            ->middleware('can:emodul.edit')
            ->name('e-modul.clear-cache');

        Route::resource('e-modul', EModulController::class);

    });
});

// Public E-Modul Reader & AI Routes (for sharing & Next.js rewrite)
Route::prefix('modul')->name('public.e-modul.')->group(function () {
    Route::get('{slug}', [EModulController::class, 'publicShow'])->name('show');
    Route::get('{slug}/pdf', [EModulController::class, 'publicPdf'])->name('pdf');
    Route::get('{slug}/cache-status', [EModulController::class, 'publicGetCacheStatus'])->name('cache-status');
    Route::post('{slug}/save-cache-batch', [EModulController::class, 'publicSaveCacheBatch'])->name('save-cache-batch');
    Route::post('{slug}/ask-ai', [EModulController::class, 'publicAskAi'])
        ->middleware('throttle:30,1')
        ->name('ask-ai');
});

// Dynamic VPS Agent Installation Script Route
Route::get('/vps-agent/{token}/install.sh', [VpsServerController::class, 'installScript'])->name('vps.install-script');

require __DIR__.'/auth.php';

