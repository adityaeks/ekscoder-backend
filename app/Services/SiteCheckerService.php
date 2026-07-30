<?php

namespace App\Services;

use App\Models\MonitoredSite;
use App\Models\SiteCheckLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class SiteCheckerService
{
    /**
     * Perform HTTP Health & SSL Check on a MonitoredSite.
     */
    public function check(MonitoredSite $site): MonitoredSite
    {
        $url = $site->url;
        $startTime = microtime(true);
        $statusCode = null;
        $errorMessage = null;
        $status = 'down';

        try {
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->withHeaders([
                    'User-Agent' => 'Ekscoder-UptimeMonitor/1.0',
                ])
                ->get($url);

            $statusCode = $response->status();
            $status = ($statusCode >= 200 && $statusCode < 400) ? 'up' : 'down';
            if ($status === 'down') {
                $errorMessage = 'HTTP Status: ' . $statusCode;
            }
        } catch (\Throwable $e) {
            $status = 'down';
            $errorMessage = $e->getMessage();
        }

        $responseTime = (int) round((microtime(true) - $startTime) * 1000);

        // Check SSL Certificate if URL is HTTPS
        $sslInfo = $this->checkSslCertificate($url);

        // Update Site Record
        $site->update([
            'status' => $status,
            'last_status_code' => $statusCode,
            'last_response_time' => $responseTime,
            'last_checked_at' => now(),
            'ssl_status' => $sslInfo['status'],
            'ssl_expires_at' => $sslInfo['expires_at'],
        ]);

        // Create Check Log Entry
        SiteCheckLog::create([
            'monitored_site_id' => $site->id,
            'status' => $status,
            'status_code' => $statusCode,
            'response_time' => $responseTime,
            'error_message' => $errorMessage,
            'checked_at' => now(),
        ]);

        return $site;
    }

    /**
     * Extract SSL Certificate Expiration Details for HTTPS URLs.
     */
    private function checkSslCertificate(string $url): array
    {
        $parsed = parse_url($url);
        $scheme = $parsed['scheme'] ?? 'http';
        $host   = $parsed['host'] ?? null;

        if (strtolower($scheme) !== 'https' || !$host) {
            return [
                'status' => 'none',
                'expires_at' => null,
            ];
        }

        try {
            $gcontext = stream_context_create([
                "ssl" => [
                    "capture_peer_cert" => true,
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ]
            ]);

            $client = @stream_socket_client(
                "ssl://" . $host . ":443",
                $errno,
                $errstr,
                5,
                STREAM_CLIENT_CONNECT,
                $gcontext
            );

            if (!$client) {
                return ['status' => 'invalid', 'expires_at' => null];
            }

            $cont = stream_context_get_params($client);
            if (!isset($cont["options"]["ssl"]["peer_certificate"])) {
                return ['status' => 'invalid', 'expires_at' => null];
            }

            $cert = openssl_x509_parse($cont["options"]["ssl"]["peer_certificate"]);
            fclose($client);

            if (!$cert || !isset($cert['validTo_time_t'])) {
                return ['status' => 'invalid', 'expires_at' => null];
            }

            $expiresAt = Carbon::createFromTimestamp($cert['validTo_time_t']);
            $sslStatus = $expiresAt->isPast() ? 'expired' : 'valid';

            return [
                'status' => $sslStatus,
                'expires_at' => $expiresAt,
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'invalid',
                'expires_at' => null,
            ];
        }
    }
}
