<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\AiSetting;
use App\Models\User;
use App\Services\AiDatabaseQueryService;
use App\Services\DatabaseSchemaService;
use App\Services\NineRouterService;
use App\Services\WebScraperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AiChatController extends Controller
{
    protected NineRouterService $nineRouterService;
    protected DatabaseSchemaService $schemaService;
    protected AiDatabaseQueryService $queryService;
    protected WebScraperService $scraperService;

    public function __construct(
        NineRouterService $nineRouterService,
        DatabaseSchemaService $schemaService,
        AiDatabaseQueryService $queryService,
        WebScraperService $scraperService
    ) {
        $this->nineRouterService = $nineRouterService;
        $this->schemaService     = $schemaService;
        $this->queryService      = $queryService;
        $this->scraperService    = $scraperService;
    }

    /**
     * Display the main AI Chat interface.
     */
    public function index()
    {
        $canManageSettings = Auth::user()->can('ai_chat.settings');

        $settings = [
            'base_url'      => $canManageSettings ? $this->nineRouterService->getBaseUrl() : '',
            'api_key'       => $canManageSettings ? $this->nineRouterService->getApiKey() : '',
            'default_model' => $this->nineRouterService->getDefaultModel(),
            'system_prompt' => $canManageSettings ? $this->nineRouterService->getSystemPrompt() : '',
        ];

        return view('admin.ai-chat.index', compact('settings'));
    }

    /**
     * Get JSON list of available models from 9Router.
     */
    public function getModels()
    {
        $models = $this->nineRouterService->getModels();

        return response()->json([
            'success' => true,
            'models'  => $models,
        ]);
    }

    /**
     * Get JSON list of conversations for current user.
     */
    public function getConversations()
    {
        $conversations = AiConversation::where('user_id', Auth::id())
            ->orderBy('is_pinned', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $conversations,
        ]);
    }

    /**
     * Store a new conversation thread.
     */
    public function storeConversation(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'model' => 'nullable|string',
        ]);

        $conversation = AiConversation::create([
            'user_id'       => Auth::id(),
            'title'         => $request->input('title', 'Percakapan Baru'),
            'model'         => $request->input('model', $this->nineRouterService->getDefaultModel()),
            'system_prompt' => $this->nineRouterService->getSystemPrompt(),
        ]);

        return response()->json([
            'success' => true,
            'data'    => $conversation,
        ]);
    }

    /**
     * Update conversation title or settings.
     */
    public function updateConversation(Request $request, $id)
    {
        $conversation = AiConversation::where('user_id', Auth::id())->findOrFail($id);

        if ($request->has('title')) {
            $conversation->title = trim($request->input('title'));
        }

        if ($request->has('model')) {
            $conversation->model = trim($request->input('model'));
        }

        if ($request->has('system_prompt')) {
            $conversation->system_prompt = $request->input('system_prompt');
        }

        if ($request->has('is_pinned')) {
            $conversation->is_pinned = $request->boolean('is_pinned');
        }

        $conversation->save();

        return response()->json([
            'success' => true,
            'message' => 'Percakapan berhasil diperbarui.',
            'data'    => $conversation,
        ]);
    }

    /**
     * Delete a conversation thread.
     */
    public function destroyConversation($id)
    {
        $conversation = AiConversation::where('user_id', Auth::id())->findOrFail($id);
        $conversation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Percakapan berhasil dihapus.',
        ]);
    }

    /**
     * Get JSON list of messages for a conversation.
     */
    public function getMessages($id)
    {
        $conversation = AiConversation::where('user_id', Auth::id())->findOrFail($id);
        $messages = $conversation->messages;

        return response()->json([
            'success'      => true,
            'conversation' => $conversation,
            'data'         => $messages,
        ]);
    }

    /**
     * Clear all messages in a conversation.
     */
    public function clearMessages($id)
    {
        $conversation = AiConversation::where('user_id', Auth::id())->findOrFail($id);
        $conversation->messages()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat pesan berhasil dibersihkan.',
        ]);
    }

    /**
     * Send user message & stream response from 9Router via Server-Sent Events (SSE).
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:ai_conversations,id',
            'message'         => 'nullable|string',
            'model'           => 'nullable|string',
            'images'          => 'nullable|array',
            'images.*'        => 'string',
        ]);

        $conversation = AiConversation::where('user_id', Auth::id())
            ->findOrFail($request->input('conversation_id'));

        $userContent = trim((string) $request->input('message', ''));
        $images = $request->input('images', []);
        if (empty($userContent) && !empty($images)) {
            $userContent = 'Tolong jelaskan dan analisa gambar ini.';
        }
        $model = $request->input('model') ?: ($conversation->model ?: $this->nineRouterService->getDefaultModel());

        // Update conversation model if changed
        if ($conversation->model !== $model) {
            $conversation->model = $model;
            $conversation->save();
        }

        // Save User Message with optional images
        AiMessage::create([
            'ai_conversation_id' => $conversation->id,
            'role'               => 'user',
            'content'            => $userContent,
            'images'             => !empty($images) ? $images : null,
        ]);

        // Auto-generate title if it's the first user message & default title
        if ($conversation->title === 'Percakapan Baru' && $conversation->messages()->count() <= 1) {
            $firstLine = trim(explode("\n", $userContent)[0]);
            $newTitle = mb_substr($firstLine, 0, 80);
            if (mb_strlen($firstLine) > 80) {
                $newTitle .= '...';
            }
            $conversation->title = $newTitle ?: 'Percakapan Baru';
            $conversation->save();
        }

        // Touch updated_at for conversation sorting
        $conversation->touch();

        // 1. Detect if user message relates to database queries (e.g. data kas, saldo, project orders, server, user)
        $dbPatterns = [
            '/\b(saldo|kas|transaksi|pemasukan|pengeluaran|keuangan)\b/i',
            '/\b(project\s*order|daftar\s*order|status\s*order|pesanan\s*klien)\b/i',
            '/\b(daftar\s*proyek|daftar\s*project|data\s*portofolio)\b/i',
            '/\b(daftar\s*vps|server\s*vps|monitoring\s*server|cpu\s*vps)\b/i',
            '/\b(daftar\s*user|daftar\s*pengguna|akun\s*pengguna|jumlah\s*user)\b/i',
            '/\b(daftar\s*artikel|daftar\s*post|kategori\s*blog)\b/i',
            '/\b(audit\s*log|log\s*aktivitas|riwayat\s*login)\b/i',
            '/\b(database|tabel\s*db|query\s*db)\b/i',
        ];
        $isDbQuery = false;
        foreach ($dbPatterns as $pattern) {
            if (preg_match($pattern, $userContent)) {
                $isDbQuery = true;
                break;
            }
        }

        // Build messages array for 9Router
        $formattedMessages = [];

        // System prompt with Strict RBAC Access Control & Conditional DB Schema
        $currentUser      = Auth::user();
        $rbacPrompt       = $this->buildRbacPrompt($currentUser);
        $baseSystemPrompt = $conversation->system_prompt ?: $this->nineRouterService->getSystemPrompt();
        $currentTimeInfo  = "WAKTU SISTEM SAAT INI: " . now()->setTimezone('Asia/Jakarta')->translatedFormat('l, d F Y H:i:s') . " WIB";

        $schemaSection = "";
        if ($isDbQuery) {
            $dbSchemaContext = $this->schemaService->getSchemaSummary($currentUser);
            $schemaSection = "\n\n=========================================\n" .
                "DATABASE ACCESS CONTEXT:\n" .
                "Berikut skema tabel database yang diizinkan untuk diakses oleh akun pengguna saat ini:\n" .
                $dbSchemaContext . "\n\n" .
                "PETUNJUK DATABASE:\n" .
                "1. Jawablah pertanyaan pengguna secara LANGSUNG, PASTI, dan SEGERA SEBUTKAN ANGKA ATAU DATA HASILNYA dalam bahasa Indonesia yang ramah.\n" .
                "2. Dilarang menuliskan kode SQL atau menyarankan query SQL kepada pengguna, karena backend Laravel telah mengeksekusi query database secara otomatis di latar belakang.\n";
        }

        $fullSystemPrompt = $baseSystemPrompt . "\n\n" .
            $currentTimeInfo . "\n\n" .
            $rbacPrompt .
            $schemaSection . "\n\n" .
            "PETUNJUK:\n" .
            "- Jawab pertanyaan pengguna secara cepat, akurat, ringkas, dan ramah dalam bahasa Indonesia.\n" .
            "- WAJIB PATUHI ATURAN HAK AKSES PENGGUNA DI ATAS: Jika pengguna bertanya mengenai fitur, modul, atau data yang DILARANG untuk akunnya, Anda WAJIB MENOLAK dan JANGAN BERIKAN DATA APAPUN.";

        if (!empty($fullSystemPrompt)) {
            $formattedMessages[] = [
                'role'    => 'system',
                'content' => $fullSystemPrompt,
            ];
        }

        // History messages (with Multimodal Vision support)
        $history = $conversation->messages()->get();
        foreach ($history as $msg) {
            if ($msg->role === 'user' && !empty($msg->images) && is_array($msg->images)) {
                $contentParts = [
                    [
                        'type' => 'text',
                        'text' => $msg->content ?: 'Lihat gambar terlampir.',
                    ],
                ];
                foreach ($msg->images as $imgUrl) {
                    $contentParts[] = [
                        'type'      => 'image_url',
                        'image_url' => [
                            'url' => $imgUrl,
                        ],
                    ];
                }
                $formattedMessages[] = [
                    'role'    => 'user',
                    'content' => $contentParts,
                ];
            } else {
                $formattedMessages[] = [
                    'role'    => $msg->role,
                    'content' => $msg->content,
                ];
            }
        }

        // Detect if user message contains URL(s) to browse/scrape
        $extractedUrls = $this->scraperService->extractUrls($userContent);
        if (!empty($extractedUrls)) {
            $browseContext = "HASIL BROWSING / PENGAMBILAN KONTEN WEBSITE SECARA REAL-TIME OLEH SISTEM:\n\n";
            // Limit up to 2 URLs per message to avoid overloading
            $targetUrls = array_slice($extractedUrls, 0, 2);
            foreach ($targetUrls as $url) {
                $browseResult = $this->scraperService->browseUrl($url);
                if ($browseResult['success']) {
                    $browseContext .= "=== URL: {$browseResult['url']} (Judul: {$browseResult['title']}) ===\n" .
                        $browseResult['content'] . "\n\n";
                } else {
                    $browseContext .= "=== URL: {$browseResult['url']} ===\n" .
                        "[Gagal mengambil konten: {$browseResult['error']}]\n\n";
                }
            }

            $browseContext .= "PETUNJUK KEPADA AI:\n" .
                "Gunakan informasi dan data dari halaman website di atas untuk menjawab dan menganalisis pertanyaan pengguna secara komprehensif, akurat, dan ramah dalam bahasa Indonesia.";

            $formattedMessages[] = [
                'role'    => 'system',
                'content' => $browseContext,
            ];
        }

        // If database query detected, execute Pass 1 SQL generation
        if ($isDbQuery) {
            try {
                // Pass 1: Ask 9Router to generate SQL query for the question
                $sqlGenMessages = $formattedMessages;
                $sqlGenMessages[] = [
                    'role'    => 'system',
                    'content' => "PENTING PASS 1: Periksa apakah pertanyaan pengguna meminta data dari fitur yang DILARANG untuk akunnya. Jika DILARANG, balas HANYA dengan kata 'FORBIDDEN'. Jika DIIZINKAN, hasilkan HANYA 1 query SQL SELECT yang valid di dalam blok kode ```sql ... ``` untuk mengambil data dari tabel yang diizinkan. JANGAN SERTAKAN TEKS PENJELASAN LAIN APAPUN."
                ];

                $sqlGenResponse = $this->nineRouterService->getChatCompletions($sqlGenMessages, $model);

                if (stripos($sqlGenResponse, 'FORBIDDEN') === false) {
                    $extractedSql = $this->queryService->extractSqlFromMarkdown($sqlGenResponse);

                    if (!empty($extractedSql)) {
                        // Execute SQL Query safely against MySQL with user permission check
                        $queryResult = $this->queryService->executeSafeQuery($extractedSql, $currentUser);

                        // Inject SQL Query Result into messages context for Pass 2 Streaming
                        $formattedMessages[] = [
                            'role'    => 'system',
                            'content' => "EKSEKUSI DATABASE REAL-TIME HASIL QUERY:\n" .
                                "Query SQL Dieksekusi: `{$queryResult['sql']}`\n" .
                                "Jumlah Baris: {$queryResult['rows']}\n" .
                                "Hasil Data Real-Time (JSON):\n```json\n" . json_encode($queryResult['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n```\n\n" .
                                "PETUNJUK PENTING KEPADA AI UNTUK JAWABAN AKHIR:\n" .
                                "1. DILARANG MENAMPILKAN ATAU MENULIS KODE SQL KEPADA PENGGUNA! Backend Laravel telah mengeksekusi query SQL di atas di latar belakang.\n" .
                                "2. LANGSUNG JAWAB PERTANYAAN PENGGUNA MENGGUNAKAN DATA REAL-TIME JSON DI ATAS dalam bahasa Indonesia yang ramah, jelas, dan lugas.\n" .
                                "3. Berikan analisis naratif atau tabel rekapitulasi data jika relevan."
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Text-to-SQL Execution Warning: " . $e->getMessage());
            }
        }

        $nineRouterService = $this->nineRouterService;
        $conversationId = $conversation->id;

        return response()->stream(function () use ($nineRouterService, $formattedMessages, $model, $conversationId) {
            // Disable output buffering
            if (function_exists('apache_setenv')) {
                @apache_setenv('no-gzip', 1);
            }
            @ini_set('zlib.output_compression', 0);
            @ini_set('implicit_flush', 1);
            for ($i = 0; $i < ob_get_level(); $i++) {
                ob_end_flush();
            }
            ob_implicit_flush(true);

            $fullAssistantResponse = '';

            try {
                $nineRouterService->streamChatCompletions(
                    $formattedMessages,
                    $model,
                    function ($chunk) use (&$fullAssistantResponse) {
                        $fullAssistantResponse .= $chunk;
                        echo "data: " . json_encode(['chunk' => $chunk]) . "\n\n";
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                    }
                );

                // Save completed Assistant Message to database
                if (!empty($fullAssistantResponse)) {
                    AiMessage::create([
                        'ai_conversation_id' => $conversationId,
                        'role'               => 'assistant',
                        'content'            => $fullAssistantResponse,
                    ]);
                }

                echo "data: [DONE]\n\n";
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();

            } catch (\Throwable $e) {
                $errorMessage = "Error: " . $e->getMessage();
                echo "data: " . json_encode(['error' => $errorMessage]) . "\n\n";
                echo "data: [DONE]\n\n";
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
            }
        }, 200, [
            'Cache-Control'     => 'no-cache, must-revalidate',
            'Content-Type'      => 'text/event-stream',
            'X-Accel-Buffering' => 'no',
            'Connection'        => 'keep-alive',
        ]);
    }

    /**
     * Save 9Router Settings.
     */
    public function saveSettings(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('ai_chat.settings');

        $request->validate([
            'base_url'      => 'required|url',
            'api_key'       => 'nullable|string',
            'default_model' => 'required|string',
            'system_prompt' => 'nullable|string',
        ]);

        AiSetting::set('base_url', trim($request->input('base_url')));
        AiSetting::set('api_key', trim($request->input('api_key')));
        AiSetting::set('default_model', trim($request->input('default_model')));
        AiSetting::set('system_prompt', trim($request->input('system_prompt')));

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan 9Router berhasil disimpan.',
        ]);
    }

    /**
     * Test connection to 9Router Gateway.
     */
    public function testConnection()
    {
        $result = $this->nineRouterService->testConnection();

        return response()->json($result);
    }

    /**
     * Build strict Role-Based Access Control (RBAC) prompt instructions for AI.
     */
    protected function buildRbacPrompt(User $user): string
    {
        $isSuperAdmin = $user->hasRole('Super Admin');
        $roles = $user->getRoleNames()->toArray();
        $roleStr = !empty($roles) ? implode(', ', $roles) : 'Pengguna';

        if ($isSuperAdmin) {
            return "================================================================================\n" .
                "INFORMASI HAK AKSES PENGGUNA (ROLE ACCESS CONTROL):\n" .
                "Nama Pengguna: {$user->name}\n" .
                "Role Akun: {$roleStr} (Super Admin - Akses Penuh ke Seluruh Fitur & Database)\n" .
                "Akun ini memiliki izin penuh ke seluruh modul sistem (Keuangan, Pesanan, Server VPS, User, Blog, dll.). Jawab seluruh pertanyaannya dengan data lengkap.\n" .
                "================================================================================";
        }

        $featureCatalog = [
            'finance' => [
                'name'        => 'Keuangan & Finansial (Saldo Kas, Pemasukan, Pengeluaran, Transaksi, Laporan Finansial)',
                'permission'  => 'finance.view',
                'description' => 'Data perputaran uang, saldo kas kantor, rincian biaya, pendapatan transaksi.',
            ],
            'orders' => [
                'name'        => 'Manajemen Pesanan & Kontrak Klien (Project Orders, Nilai Kontrak, Pembayaran DP)',
                'permission'  => 'orders.view',
                'description' => 'Data pesanan proyek, nilai kontrak kerja, deadline pesanan klien, dan budget.',
            ],
            'projects' => [
                'name'        => 'Portofolio Proyek Web',
                'permission'  => 'projects.view',
                'description' => 'Daftar proyek portofolio website, tech stack, dan data etalase karya.',
            ],
            'posts' => [
                'name'        => 'Blog & Artikel (Content Management)',
                'permission'  => 'posts.view',
                'description' => 'Artikel blog, kategori artikel, draft konten, dan artikel terpublikasi.',
            ],
            'vps' => [
                'name'        => 'Infrastruktur Server & Monitoring VPS',
                'permission'  => 'vps.view',
                'description' => 'Server VPS, IP address server, spesifikasi teknis hardware, dan pemantauan server.',
            ],
            'sites' => [
                'name'        => 'Monitoring Uptime & Status Website',
                'permission'  => 'sites.view',
                'description' => 'Status aktif/down website yang dipantau, response time, dan riwayat status web.',
            ],
            'cloudflare' => [
                'name'        => 'Cloudflare & DNS Records',
                'permission'  => 'cloudflare.view',
                'description' => 'Domain DNS, zone Cloudflare, konfigurasi record IP, dan keamanan web.',
            ],
            'users' => [
                'name'        => 'Data Pengguna & Akun (User Management)',
                'permission'  => 'users.view',
                'description' => 'Daftar pengguna sistem, alamat email pengguna, data akun, dan kredensial user.',
            ],
            'roles' => [
                'name'        => 'Manajemen Role & Hak Akses (Roles & Permissions)',
                'permission'  => 'roles.view',
                'description' => 'Daftar role sistem, penugasan izin permission, dan struktur hak akses.',
            ],
            'logs' => [
                'name'        => 'Activity Logs & Audit Feed',
                'permission'  => 'logs.view',
                'description' => 'Riwayat login, logout, log aktivitas aksi user, dan audit trail sistem.',
            ],
            'emodul' => [
                'name'        => 'Pustaka E-Modul Pembelajaran',
                'permission'  => 'emodul.view',
                'description' => 'Buku digital e-modul, materi bab, panduan pembelajaran, dan konten edukasi.',
            ],
            'ai_cs' => [
                'name'        => 'AI Customer Service Logs & Konfigurasi Bot',
                'permission'  => 'ai_cs.view',
                'description' => 'Log chat pelanggan/pengunjung landing page dan konfigurasi prompt bot CS.',
            ],
            'notes' => [
                'name'        => 'Catatan Internal Tim (Notes)',
                'permission'  => 'notes.view',
                'description' => 'Catatan dan memo internal operasional tim.',
            ],
            'calendar' => [
                'name'        => 'Kalender & Jadwal Agenda Kerja',
                'permission'  => 'calendar.view',
                'description' => 'Jadwal meeting dan kalender deadline internal.',
            ],
            'api' => [
                'name'        => 'API Developer Preview',
                'permission'  => 'api.preview',
                'description' => 'Pratinjau endpoint developer API publik.',
            ],
        ];

        $allowedFeatures = [];
        $forbiddenFeatures = [];

        foreach ($featureCatalog as $key => $item) {
            if ($user->can($item['permission'])) {
                $allowedFeatures[] = "- [DIIZINKAN] {$item['name']}";
            } else {
                $forbiddenFeatures[] = "- [DILARANG KERAS] {$item['name']}: {$item['description']}";
            }
        }

        $allowedText = !empty($allowedFeatures)
            ? implode("\n", $allowedFeatures)
            : "- (Tidak ada modul internal khusus yang diizinkan; hanya boleh percakapan umum & bantuan teknis/koding non-data sistem).";

        $forbiddenText = !empty($forbiddenFeatures)
            ? implode("\n", $forbiddenFeatures)
            : "- (Tidak ada larangan khusus).";

        return "================================================================================\n" .
            "ATURAN MUTLAK HAK AKSES PENGGUNA (ROLE & PERMISSION ACCESS CONTROL):\n" .
            "Nama Pengguna: {$user->name}\n" .
            "Role Akun: {$roleStr}\n\n" .
            "FITUR YANG DIIZINKAN UNTUK PENGGUNA INI:\n" .
            $allowedText . "\n\n" .
            "FITUR & DATA YANG DILARANG / TIDAK BOLEH DIAKSES OLEH PENGGUNA INI:\n" .
            $forbiddenText . "\n\n" .
            "INSTRUKSI KEAMANAN MUTLAK KEPADA AI:\n" .
            "1. CEK HAK AKSES DULU: Sebelum menjawab, Anda WAJIB memeriksa apakah pertanyaan pengguna meminta data, informasi, atau pembahasan seputar 'FITUR & DATA YANG DILARANG' di atas.\n" .
            "2. PENOLAKAN AKSES: JIKA PENGGUNA MENANYAKAN TENTANG FITUR/DATA YANG DILARANG (misal: menanyakan saldo kas/keuangan padahal dilarang, atau menanyakan order klien, user, server VPS, dll padahal dilarang):\n" .
            "   - Anda TIDAK BOLEH MENJAWAB pertanyaan tersebut!\n" .
            "   - DILARANG KERAS membocorkan data, angka, jumlah, ringkasan, maupun informasi apapun mengenai modul yang dilarang tersebut.\n" .
            "   - Berikan respon penolakan yang tegas, sopan, dan santun dalam bahasa Indonesia.\n" .
            "   - Format respon penolakan yang wajib Anda gunakan:\n" .
            "     'Maaf, akun Anda ({$roleStr}) tidak memiliki izin akses (permission) untuk melihat informasi atau data [Nama Fitur]. Silakan hubungi Super Admin untuk penyesuaian hak akses.'\n" .
            "3. JIKA DIIZINKAN ATAU PERTANYAAN UMUM: Jawablah dengan ramah, cerdas, akurat, dan solutif sebagaimana layaknya asisten profesional.\n" .
            "================================================================================";
    }
}
