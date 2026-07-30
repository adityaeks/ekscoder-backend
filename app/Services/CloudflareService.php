<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Exception;

class CloudflareService
{
    protected ?string $token;
    protected ?string $accountId;
    protected string $baseUrl;

    public function __construct()
    {
        $this->token = config('services.cloudflare.token');
        $this->accountId = config('services.cloudflare.account_id');
        $this->baseUrl = config('services.cloudflare.base_url', 'https://api.cloudflare.com/client/v4/');
    }

    /**
     * Check if Cloudflare API credentials are set.
     */
    public function isConfigured(): bool
    {
        return !empty($this->token);
    }

    /**
     * Get pre-configured HTTP Client.
     */
    protected function client(): PendingRequest
    {
        if (!$this->isConfigured()) {
            throw new Exception('Cloudflare API Token belum dikonfigurasi di file .env (CLOUDFLARE_API_TOKEN).');
        }

        return Http::withToken($this->token)
            ->baseUrl($this->baseUrl)
            ->acceptJson()
            ->timeout(15);
    }

    /**
     * Handle Cloudflare API Response.
     */
    protected function handleResponse($response): array
    {
        $data = $response->json();

        if ($response->failed()) {
            $errorMessage = $data['errors'][0]['message'] ?? $response->reason() ?? 'Cloudflare API Request Failed';
            throw new Exception("Cloudflare API Error ({$response->status()}): {$errorMessage}");
        }

        if (isset($data['success']) && !$data['success']) {
            $errorMessage = $data['errors'][0]['message'] ?? 'Unknown Cloudflare API Error';
            throw new Exception("Cloudflare API Error: {$errorMessage}");
        }

        return $data;
    }

    // ==========================================
    // 🌐 ZONE / DOMAIN MANAGEMENT
    // ==========================================

    /**
     * Get list of zones/domains.
     */
    public function getZones(array $queryParams = []): array
    {
        if ($this->accountId && !isset($queryParams['account.id'])) {
            $queryParams['account.id'] = $this->accountId;
        }
        $queryParams['per_page'] = $queryParams['per_page'] ?? 50;

        $response = $this->client()->get('zones', $queryParams);
        return $this->handleResponse($response);
    }

    /**
     * Get single zone details.
     */
    public function getZone(string $zoneId): array
    {
        $response = $this->client()->get("zones/{$zoneId}");
        return $this->handleResponse($response);
    }

    /**
     * Add a new zone/domain to Cloudflare.
     */
    public function createZone(string $name, string $type = 'full', bool $jumpStart = true): array
    {
        $payload = [
            'name' => strtolower(trim($name)),
            'type' => $type,
            'jump_start' => $jumpStart,
        ];

        if ($this->accountId) {
            $payload['account'] = ['id' => $this->accountId];
        }

        $response = $this->client()->post('zones', $payload);
        return $this->handleResponse($response);
    }

    /**
     * Delete a zone/domain from Cloudflare.
     */
    public function deleteZone(string $zoneId): array
    {
        $response = $this->client()->delete("zones/{$zoneId}");
        return $this->handleResponse($response);
    }

    // ==========================================
    // 📡 DNS RECORDS & 🔄 PROXY TOGGLE
    // ==========================================

    /**
     * Get all DNS records for a zone.
     */
    public function getDnsRecords(string $zoneId, array $queryParams = []): array
    {
        $queryParams['per_page'] = $queryParams['per_page'] ?? 100;
        $response = $this->client()->get("zones/{$zoneId}/dns_records", $queryParams);
        return $this->handleResponse($response);
    }

    /**
     * Create a DNS record.
     */
    public function createDnsRecord(string $zoneId, array $data): array
    {
        $payload = [
            'type' => strtoupper($data['type']),
            'name' => trim($data['name']),
            'content' => trim($data['content']),
            'ttl' => (int) ($data['ttl'] ?? 1), // 1 = Automatic
            'proxied' => (bool) ($data['proxied'] ?? false),
        ];

        if (isset($data['priority']) && in_array(strtoupper($data['type']), ['MX', 'URI'])) {
            $payload['priority'] = (int) $data['priority'];
        }

        if (isset($data['comment'])) {
            $payload['comment'] = $data['comment'];
        }

        $response = $this->client()->post("zones/{$zoneId}/dns_records", $payload);
        return $this->handleResponse($response);
    }

    /**
     * Update an existing DNS record.
     */
    public function updateDnsRecord(string $zoneId, string $recordId, array $data): array
    {
        $payload = [
            'type' => strtoupper($data['type']),
            'name' => trim($data['name']),
            'content' => trim($data['content']),
            'ttl' => (int) ($data['ttl'] ?? 1),
            'proxied' => (bool) ($data['proxied'] ?? false),
        ];

        if (isset($data['priority']) && in_array(strtoupper($data['type']), ['MX', 'URI'])) {
            $payload['priority'] = (int) $data['priority'];
        }

        if (isset($data['comment'])) {
            $payload['comment'] = $data['comment'];
        }

        $response = $this->client()->put("zones/{$zoneId}/dns_records/{$recordId}", $payload);
        return $this->handleResponse($response);
    }

    /**
     * Toggle Cloudflare Proxy Status (Orange Cloud vs Grey Cloud).
     */
    public function toggleProxyStatus(string $zoneId, string $recordId, bool $proxied): array
    {
        $response = $this->client()->patch("zones/{$zoneId}/dns_records/{$recordId}", [
            'proxied' => $proxied,
        ]);
        return $this->handleResponse($response);
    }

    /**
     * Delete a DNS record.
     */
    public function deleteDnsRecord(string $zoneId, string $recordId): array
    {
        $response = $this->client()->delete("zones/{$zoneId}/dns_records/{$recordId}");
        return $this->handleResponse($response);
    }

    // ==========================================
    // 🧹 CACHE PURGE
    // ==========================================

    /**
     * Purge cache for a zone.
     */
    public function purgeCache(string $zoneId, bool $purgeEverything = true, array $files = []): array
    {
        $payload = [];
        if ($purgeEverything) {
            $payload['purge_everything'] = true;
        } elseif (!empty($files)) {
            $payload['files'] = array_values(array_filter($files));
        } else {
            throw new Exception('Pilih "Purge Everything" atau berikan setidaknya 1 URL file untuk dipurge.');
        }

        $response = $this->client()->post("zones/{$zoneId}/purge_cache", $payload);
        return $this->handleResponse($response);
    }

    // ==========================================
    // 🔐 SSL & SECURITY SETTINGS
    // ==========================================

    /**
     * Get SSL Setting (off, flexible, full, strict).
     */
    public function getSslSetting(string $zoneId): array
    {
        $response = $this->client()->get("zones/{$zoneId}/settings/ssl");
        return $this->handleResponse($response);
    }

    /**
     * Update SSL Setting.
     */
    public function updateSslSetting(string $zoneId, string $mode): array
    {
        $validModes = ['off', 'flexible', 'full', 'strict'];
        if (!in_array($mode, $validModes)) {
            throw new Exception('SSL Mode tidak valid. Pilih: off, flexible, full, atau strict.');
        }

        $response = $this->client()->patch("zones/{$zoneId}/settings/ssl", [
            'value' => $mode,
        ]);
        return $this->handleResponse($response);
    }

    /**
     * Get Security Level setting.
     */
    public function getSecurityLevel(string $zoneId): array
    {
        $response = $this->client()->get("zones/{$zoneId}/settings/security_level");
        return $this->handleResponse($response);
    }

    /**
     * Update Security Level setting.
     */
    public function updateSecurityLevel(string $zoneId, string $level): array
    {
        $validLevels = ['off', 'essentially_off', 'low', 'medium', 'high', 'under_attack'];
        if (!in_array($level, $validLevels)) {
            throw new Exception('Security Level tidak valid.');
        }

        $response = $this->client()->patch("zones/{$zoneId}/settings/security_level", [
            'value' => $level,
        ]);
        return $this->handleResponse($response);
    }

    /**
     * Get Zone Settings summary (SSL, Security Level, Development Mode, HTTPS Redirect).
     */
    public function getZoneSettingsSummary(string $zoneId): array
    {
        $response = $this->client()->get("zones/{$zoneId}/settings");
        return $this->handleResponse($response);
    }
}
