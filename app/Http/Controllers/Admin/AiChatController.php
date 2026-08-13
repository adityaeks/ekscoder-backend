<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\AiSetting;
use App\Services\AiDatabaseQueryService;
use App\Services\DatabaseSchemaService;
use App\Services\NineRouterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AiChatController extends Controller
{
    protected NineRouterService $nineRouterService;
    protected DatabaseSchemaService $schemaService;
    protected AiDatabaseQueryService $queryService;

    public function __construct(
        NineRouterService $nineRouterService,
        DatabaseSchemaService $schemaService,
        AiDatabaseQueryService $queryService
    ) {
        $this->nineRouterService = $nineRouterService;
        $this->schemaService     = $schemaService;
        $this->queryService      = $queryService;
    }

    /**
     * Display the main AI Chat interface.
     */
    public function index()
    {
        $settings = [
            'base_url'      => $this->nineRouterService->getBaseUrl(),
            'api_key'       => $this->nineRouterService->getApiKey(),
            'default_model' => $this->nineRouterService->getDefaultModel(),
            'system_prompt' => $this->nineRouterService->getSystemPrompt(),
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
            'message'         => 'required|string',
            'model'           => 'nullable|string',
        ]);

        $conversation = AiConversation::where('user_id', Auth::id())
            ->findOrFail($request->input('conversation_id'));

        $userContent = trim($request->input('message'));
        $model = $request->input('model') ?: ($conversation->model ?: $this->nineRouterService->getDefaultModel());

        // Update conversation model if changed
        if ($conversation->model !== $model) {
            $conversation->model = $model;
            $conversation->save();
        }

        // Save User Message
        AiMessage::create([
            'ai_conversation_id' => $conversation->id,
            'role'               => 'user',
            'content'            => $userContent,
        ]);

        // Auto-generate title if it's the first user message & default title
        if ($conversation->title === 'Percakapan Baru' && $conversation->messages()->count() <= 1) {
            $newTitle = mb_substr($userContent, 0, 40);
            if (mb_strlen($userContent) > 40) {
                $newTitle .= '...';
            }
            $conversation->title = $newTitle;
            $conversation->save();
        }

        // Touch updated_at for conversation sorting
        $conversation->touch();

        // Build messages array for 9Router
        $formattedMessages = [];

        // System prompt with DB Schema Context
        $baseSystemPrompt = $conversation->system_prompt ?: $this->nineRouterService->getSystemPrompt();
        $dbSchemaContext  = $this->schemaService->getSchemaSummary();

        $fullSystemPrompt = $baseSystemPrompt . "\n\n" .
            "=========================================\n" .
            "DATABASE ACCESS CONTEXT:\n" .
            "Anda memiliki akses penuh untuk menganalisis data database MySQL aplikasi ini. Berikut skema tabel yang tersedia:\n" .
            $dbSchemaContext . "\n\n" .
            "PETUNJUK PENTING:\n" .
            "1. Jawablah pertanyaan pengguna secara LANGSUNG, PASTI, dan SEGERA SEBUTKAN ANGKA ATAU DATA HASILNYA dalam bahasa Indonesia yang ramah.\n" .
            "2. Dilarang menuliskan kode SQL atau menyarankan query SQL kepada pengguna, karena backend Laravel telah mengeksekusi query database secara otomatis di latar belakang.";

        if (!empty($fullSystemPrompt)) {
            $formattedMessages[] = [
                'role'    => 'system',
                'content' => $fullSystemPrompt,
            ];
        }

        // History messages
        $history = $conversation->messages()->get();
        foreach ($history as $msg) {
            $formattedMessages[] = [
                'role'    => $msg->role,
                'content' => $msg->content,
            ];
        }

        // Detect if user message relates to database queries (e.g. kas, transaksi, order, user, server, blog, total, saldo)
        $dbKeywords = ['kas', 'transaksi', 'uang', 'pemasukan', 'pengeluaran', 'saldo', 'total', 'proyek', 'project', 'order', 'client', 'server', 'vps', 'blog', 'artikel', 'user', 'pengguna', 'database', 'db', 'keuangan', 'laporan', 'berapa'];
        $isDbQuery = false;
        foreach ($dbKeywords as $kw) {
            if (mb_stripos($userContent, $kw) !== false) {
                $isDbQuery = true;
                break;
            }
        }

        if ($isDbQuery) {
            try {
                // Pass 1: Ask 9Router to generate SQL query for the question
                $sqlGenMessages = $formattedMessages;
                $sqlGenMessages[] = [
                    'role'    => 'system',
                    'content' => "PENTING PASS 1: Hasilkan HANYA 1 query SQL SELECT yang valid di dalam blok kode ```sql ... ``` untuk mengambil data dari database yang dibutuhkan pengguna. JANGAN SERTAKAN TEKS PENJELASAN LAIN APAPUN."
                ];

                $sqlGenResponse = $this->nineRouterService->getChatCompletions($sqlGenMessages, $model);
                $extractedSql = $this->queryService->extractSqlFromMarkdown($sqlGenResponse);

                if (!empty($extractedSql)) {
                    // Execute SQL Query safely against MySQL
                    $queryResult = $this->queryService->executeSafeQuery($extractedSql);

                    // Inject SQL Query Result into messages context for Pass 2 Streaming
                    $formattedMessages[] = [
                        'role'    => 'system',
                        'content' => "EKSEKUSI DATABASE REAL-TIME HASIL QUERY:\n" .
                            "Query SQL Dieksekusi: `{$queryResult['sql']}`\n" .
                            "Jumlah Baris: {$queryResult['rows']}\n" .
                            "Hasil Data Real-Time (JSON):\n```json\n" . json_encode($queryResult['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n```\n\n" .
                            "PETUNJUK PENTING KEPADA AI UNTUK JAWABAN AKHIR:\n" .
                            "1. DILARANG MENAMPILKAN ATAU MENULIS KODE SQL KEPADA PENGGUNA! Backend Laravel telah mengeksekusi query SQL di atas di latar belakang.\n" .
                            "2. LANGSUNG JAWAB PERTANYAAN PENGGUNA MENGGUNAKAN DATA REAL-TIME JSON DI ATAS dalam bahasa Indonesia yang ramah, jelas, dan lugas (contoh: 'Total server VPS yang terdaftar di sistem saat ini adalah 2 server.').\n" .
                            "3. Berikan analisis naratif atau tabel rekapitulasi data jika relevan."
                    ];
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
}
