<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebScraperService
{
    /**
     * Maximum characters to return from scraped content to protect token limits.
     */
    protected int $maxContentLength = 7000;

    /**
     * Extract URLs from given text.
     */
    public function extractUrls(string $text): array
    {
        $pattern = '/https?:\/\/[^\s<>"\'\)\]\}]+/i';
        preg_match_all($pattern, $text, $matches);

        if (empty($matches[0])) {
            return [];
        }

        // Clean trailing punctuation that might have been captured
        $urls = [];
        foreach ($matches[0] as $url) {
            $cleaned = rtrim($url, '.,;:!?');
            if (filter_var($cleaned, FILTER_VALIDATE_URL)) {
                $urls[] = $cleaned;
            }
        }

        return array_values(array_unique($urls));
    }

    /**
     * Browse and extract clean readable text from a URL.
     */
    public function browseUrl(string $url): array
    {
        // 1. SSRF Safety Check
        if (!$this->isSafeUrl($url, $errorReason)) {
            return [
                'success' => false,
                'url'     => $url,
                'error'   => "Akses URL diblokir demi keamanan: {$errorReason}",
                'content' => null,
            ];
        }

        try {
            $response = Http::timeout(12)
                ->withHeaders([
                    'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36 (ekscoder-ai-agent)',
                    'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,text/plain;q=0.8,*/*;q=0.7',
                    'Accept-Language' => 'id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
                ])
                ->get($url);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'url'     => $url,
                    'error'   => "Gagal mengakses URL (HTTP Status: {$response->status()})",
                    'content' => null,
                ];
            }

            $contentType = $response->header('Content-Type') ?? '';
            $body = $response->body();

            // If it's JSON response
            if (str_contains($contentType, 'application/json') || (str_starts_with(trim($body), '{') && str_ends_with(trim($body), '}'))) {
                $decoded = json_decode($body, true);
                $formattedJson = $decoded ? json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $body;
                return [
                    'success' => true,
                    'url'     => $url,
                    'title'   => 'JSON Response',
                    'content' => mb_substr($formattedJson, 0, $this->maxContentLength),
                ];
            }

            // If it's Plain Text
            if (str_contains($contentType, 'text/plain')) {
                return [
                    'success' => true,
                    'url'     => $url,
                    'title'   => 'Plain Text',
                    'content' => mb_substr(trim($body), 0, $this->maxContentLength),
                ];
            }

            // HTML Parsing & Sanitization
            $extracted = $this->extractCleanContentFromHtml($body);

            return [
                'success' => true,
                'url'     => $url,
                'title'   => $extracted['title'] ?: 'Web Page',
                'content' => $extracted['content'],
            ];

        } catch (\Throwable $e) {
            Log::warning("WebScraperService error accessing {$url}: " . $e->getMessage());
            return [
                'success' => false,
                'url'     => $url,
                'error'   => "Terjadi kesalahan saat memproses URL: " . $e->getMessage(),
                'content' => null,
            ];
        }
    }

    /**
     * Check if a URL is safe to fetch (Prevents SSRF attacks to internal network / localhost).
     */
    protected function isSafeUrl(string $url, ?string &$errorReason = null): bool
    {
        $parts = parse_url($url);

        if (!$parts || empty($parts['host'])) {
            $errorReason = 'Format URL tidak valid.';
            return false;
        }

        $scheme = strtolower($parts['scheme'] ?? '');
        if (!in_array($scheme, ['http', 'https'], true)) {
            $errorReason = 'Hanya protokol HTTP dan HTTPS yang diizinkan.';
            return false;
        }

        $host = strtolower($parts['host']);

        // Block obvious local hostnames
        if (in_array($host, ['localhost', '127.0.0.1', '::1', '0.0.0.0', 'host.docker.internal'], true)) {
            $errorReason = 'Akses ke localhost tidak diizinkan.';
            return false;
        }

        if (str_ends_with($host, '.local') || str_ends_with($host, '.internal') || str_ends_with($host, '.test')) {
            $errorReason = 'Akses ke domain lokal/internal tidak diizinkan.';
            return false;
        }

        // Resolve DNS and check if IP is private or reserved
        $ips = @gethostbynamel($host);
        if ($ips === false || empty($ips)) {
            // If DNS lookup fails, allow fetch attempt (it will fail safely in HTTP client if invalid)
            return true;
        }

        foreach ($ips as $ip) {
            if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                $errorReason = "Host terhubung ke IP privat/terlarang ({$ip}).";
                return false;
            }
        }

        return true;
    }

    /**
     * Parse HTML, strip unwanted tags, and convert to structured readable text/markdown.
     */
    protected function extractCleanContentFromHtml(string $html): array
    {
        if (empty(trim($html))) {
            return ['title' => '', 'content' => 'Halaman web kosong.'];
        }

        $internalErrors = libxml_use_internal_errors(true);
        $dom = new \DOMDocument();

        // Convert encoding to UTF-8
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);

        // 1. Extract Title
        $title = '';
        $titleNodes = $dom->getElementsByTagName('title');
        if ($titleNodes->length > 0) {
            $title = trim($titleNodes->item(0)->textContent);
        }

        // 2. Remove unwanted tags
        $removeTags = ['script', 'style', 'noscript', 'nav', 'footer', 'svg', 'iframe', 'header', 'aside', 'form', 'button', 'select', 'canvas'];
        foreach ($removeTags as $tag) {
            $nodes = $dom->getElementsByTagName($tag);
            while ($nodes->length > 0) {
                $node = $nodes->item(0);
                $node->parentNode->removeChild($node);
            }
        }

        // 3. Extract text content from main/body
        $body = $dom->getElementsByTagName('body')->item(0) ?: $dom->documentElement;
        $rawText = $body ? $body->textContent : $dom->textContent;

        // 4. Clean up whitespace and newlines
        $lines = explode("\n", $rawText);
        $cleanLines = [];
        foreach ($lines as $line) {
            $trimmed = trim(preg_replace('/\s+/', ' ', $line));
            if (!empty($trimmed)) {
                $cleanLines[] = $trimmed;
            }
        }

        $cleanedContent = implode("\n", $cleanLines);

        // Limit size
        if (mb_strlen($cleanedContent) > $this->maxContentLength) {
            $cleanedContent = mb_substr($cleanedContent, 0, $this->maxContentLength) . "\n... [Konten dipotong karena mencapai batas panjang maksimum]";
        }

        return [
            'title'   => $title,
            'content' => $cleanedContent ?: 'Tidak ditemukan konten teks pada halaman web ini.',
        ];
    }
}
