<?php

namespace App\Services;

use App\Models\AiSetting;
use Illuminate\Support\Facades\Http;

class NineRouterService
{
    /**
     * Get configured Base URL for 9Router.
     */
    public function getBaseUrl(): string
    {
        $url = rtrim(AiSetting::get('base_url', 'http://localhost:20128/v1'), '/');
        return $url;
    }

    /**
     * Get configured API Key for 9Router.
     */
    public function getApiKey(): string
    {
        return AiSetting::get('api_key', '9router-local-key');
    }

    /**
     * Get default model name.
     */
    public function getDefaultModel(): string
    {
        return AiSetting::get('default_model', 'Spark');
    }

    /**
     * Get system prompt.
     */
    public function getSystemPrompt(): string
    {
        return AiSetting::get('system_prompt', 'Anda adalah asisten AI profesional dan serba bisa yang terintegrasi ke dalam sistem Ekscoder. Berikan jawaban yang tepat, terstruktur, ramah, dan solutif dalam bahasa Indonesia.');
    }

    /**
     * Fetch available models from 9Router GET /models.
     */
    public function getModels(): array
    {
        $baseUrl = $this->getBaseUrl();
        $apiKey = $this->getApiKey();

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Accept' => 'application/json',
            ])->timeout(5)->get("{$baseUrl}/models");

            if ($response->successful()) {
                $data = $response->json();
                $models = [];
                if (isset($data['data']) && is_array($data['data'])) {
                    foreach ($data['data'] as $m) {
                        if (isset($m['id'])) {
                            $models[] = $m['id'];
                        }
                    }
                }
                return $models;
            }
        } catch (\Throwable $e) {
            // Fallback
        }

        return [];
    }

    /**
     * Test connection to 9Router gateway.
     */
    public function testConnection(): array
    {
        $baseUrl = $this->getBaseUrl();
        $apiKey = $this->getApiKey();

        try {
            // Attempt GET /v1/models
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Accept' => 'application/json',
            ])->timeout(5)->get("{$baseUrl}/models");

            if ($response->successful()) {
                $data = $response->json();
                $models = [];
                if (isset($data['data']) && is_array($data['data'])) {
                    foreach ($data['data'] as $m) {
                        if (isset($m['id'])) {
                            $models[] = $m['id'];
                        }
                    }
                }

                return [
                    'success' => true,
                    'message' => 'Terhubung dengan 9Router Gateway!',
                    'models'  => $models,
                ];
            }

            if ($response->status() === 401) {
                return [
                    'success' => false,
                    'message' => "Gagal Otentikasi 9Router (401 Unauthorized): API Key tidak valid. Silakan periksa API Key Anda di Pengaturan API.",
                    'models'  => [],
                ];
            }

            return [
                'success' => false,
                'message' => "Gagal terhubung ke 9Router ({$baseUrl}): Status HTTP " . $response->status(),
                'models'  => [],
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => "Gagal terhubung ke 9Router di '{$baseUrl}'. Pastikan server 9Router telah berjalan di port tersebut. Error: " . $e->getMessage(),
                'models'  => [],
            ];
        }
    }

    /**
     * Stream Chat Completions via cURL and Server-Sent Events (SSE).
     *
     * @param array $messages List of OpenAI formatted messages [['role' => '...', 'content' => '...']]
     * @param string|null $model Model ID
     * @param callable $onChunk Callback function(string $contentChunk)
     * @return string Complete response text concatenated
     */
    public function streamChatCompletions(array $messages, ?string $model = null, ?callable $onChunk = null): string
    {
        $baseUrl = $this->getBaseUrl();
        $apiKey = $this->getApiKey();
        $model = $model ?: $this->getDefaultModel();

        $endpoint = "{$baseUrl}/chat/completions";

        $payload = json_encode([
            'model' => $model,
            'messages' => $messages,
            'stream' => true,
        ]);

        $fullContent = '';
        $rawResponseBody = '';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);

        $headers = [
            'Content-Type: application/json',
            'Accept: text/event-stream',
        ];
        if (!empty($apiKey)) {
            $headers[] = "Authorization: Bearer {$apiKey}";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $buffer = '';

        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) use (&$fullContent, &$buffer, &$rawResponseBody, $onChunk) {
            $rawResponseBody .= $data;
            $buffer .= $data;

            while (($linePos = strpos($buffer, "\n")) !== false) {
                $line = substr($buffer, 0, $linePos);
                $buffer = substr($buffer, $linePos + 1);

                $line = trim($line);
                if (empty($line) || strpos($line, 'data:') !== 0) {
                    continue;
                }

                $jsonStr = trim(substr($line, 5));
                if ($jsonStr === '[DONE]') {
                    break;
                }

                $json = json_decode($jsonStr, true);
                $textChunk = null;

                if (isset($json['choices'][0]['delta']['content'])) {
                    $textChunk = $json['choices'][0]['delta']['content'];
                } elseif (isset($json['choices'][0]['text'])) {
                    $textChunk = $json['choices'][0]['text'];
                } elseif (isset($json['choices'][0]['message']['content'])) {
                    $textChunk = $json['choices'][0]['message']['content'];
                }

                if ($textChunk !== null) {
                    $fullContent .= $textChunk;
                    if ($onChunk) {
                        $onChunk($textChunk);
                    }
                }
            }
            return strlen($data);
        });

        curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception("Gagal berkomunikasi dengan 9Router Gateway: {$error}");
        }

        curl_close($ch);

        if ($httpCode !== 200) {
            $errMessage = "HTTP Error {$httpCode}";
            $jsonErr = json_decode($rawResponseBody, true);
            if (isset($jsonErr['error']['message'])) {
                $errMessage = $jsonErr['error']['message'];
            } elseif (isset($jsonErr['message'])) {
                $errMessage = $jsonErr['message'];
            }
            throw new \Exception("9Router Response Error ({$httpCode}): {$errMessage}");
        }

        return $fullContent;
    }

    /**
     * Get synchronous (non-streaming) Chat Completions from 9Router.
     *
     * @param array $messages
     * @param string|null $model
     * @param int $timeout
     * @return string
     * @throws \Exception
     */
    public function getChatCompletions(array $messages, ?string $model = null, int $timeout = 90, array $extraParams = []): string
    {
        $baseUrl = $this->getBaseUrl();
        $apiKey = $this->getApiKey();
        $model = $model ?: $this->getDefaultModel();

        $endpoint = "{$baseUrl}/chat/completions";

        $payload = array_merge([
            'model' => $model,
            'messages' => $messages,
            'stream' => false,
        ], $extraParams);

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->timeout($timeout)->post($endpoint, $payload);

        if ($response->successful()) {
            $data = $response->json();
            return $data['choices'][0]['message']['content'] ?? '';
        }

        $errorMsg = $response->body();
        $json = $response->json();
        if (is_array($json)) {
            if (isset($json['error']['message'])) {
                $errorMsg = $json['error']['message'];
            } elseif (isset($json['message'])) {
                $errorMsg = $json['message'];
            }
        }

        throw new \Exception("9Router HTTP Error (" . $response->status() . "): " . $errorMsg);
    }
}
