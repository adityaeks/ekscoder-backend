<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Exception;

class DomainExpirationService
{
    /**
     * Get domain expiration information (cached for 3 days by default).
     *
     * @param string $domain
     * @param bool $forceRefresh
     * @return array
     */
    public function getExpirationInfo(string $domain, bool $forceRefresh = false): array
    {
        $cleanDomain = strtolower(trim($domain));
        if (empty($cleanDomain)) {
            return $this->buildResult(null);
        }

        $cacheKey = "domain_exp_{$cleanDomain}";

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, 86400 * 3, function () use ($cleanDomain) {
            $expiresAt = $this->fetchExpirationDate($cleanDomain);
            return $this->buildResult($expiresAt);
        });
    }

    /**
     * Fetch expiration date using RDAP API first, falling back to Port 43 WHOIS Socket.
     *
     * @param string $domain
     * @return Carbon|null
     */
    protected function fetchExpirationDate(string $domain): ?Carbon
    {
        // 1. Primary Method: RDAP API
        $rdapDate = $this->fetchViaRdap($domain);
        if ($rdapDate) {
            return $rdapDate;
        }

        // 2. Secondary Method: Socket WHOIS (Port 43)
        return $this->fetchViaWhoisSocket($domain);
    }

    /**
     * Fetch expiration date via RDAP (RFC 7480/7482/7483).
     *
     * @param string $domain
     * @return Carbon|null
     */
    protected function fetchViaRdap(string $domain): ?Carbon
    {
        try {
            $url = "https://rdap.org/domain/{$domain}";
            $response = Http::timeout(4)
                ->withOptions(['allow_redirects' => true, 'verify' => false])
                ->withHeaders(['Accept' => 'application/rdap+json, application/json'])
                ->get($url);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['events']) && is_array($data['events'])) {
                    foreach ($data['events'] as $event) {
                        $action = strtolower($event['eventAction'] ?? '');
                        if (in_array($action, ['expiration', 'registrar expiration', 'expiration date'])) {
                            if (!empty($event['eventDate'])) {
                                return Carbon::parse($event['eventDate']);
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            // Ignore RDAP failure and fall through to WHOIS
        }

        return null;
    }

    /**
     * Fetch expiration date via Socket WHOIS on Port 43.
     *
     * @param string $domain
     * @return Carbon|null
     */
    protected function fetchViaWhoisSocket(string $domain): ?Carbon
    {
        try {
            $whoisServers = [
                'com'     => 'whois.verisign-grs.com',
                'net'     => 'whois.verisign-grs.com',
                'org'     => 'whois.pir.org',
                'info'    => 'whois.afilias.net',
                'biz'     => 'whois.biz',
                'io'      => 'whois.nic.io',
                'id'      => 'whois.pandi.or.id',
                'my.id'   => 'whois.pandi.or.id',
                'co.id'   => 'whois.pandi.or.id',
                'web.id'  => 'whois.pandi.or.id',
                'biz.id'  => 'whois.pandi.or.id',
                'ac.id'   => 'whois.pandi.or.id',
                'sch.id'  => 'whois.pandi.or.id',
                'site'    => 'whois.nic.site',
                'xyz'     => 'whois.nic.xyz',
                'online'  => 'whois.nic.online',
                'tech'    => 'whois.nic.tech',
                'store'   => 'whois.nic.store',
                'app'     => 'whois.nic.google',
                'dev'     => 'whois.nic.google',
                'me'      => 'whois.nic.me',
                'co'      => 'whois.nic.co',
            ];

            $parts = explode('.', $domain);
            $tld = end($parts);

            if (count($parts) > 2) {
                $twoPartTld = implode('.', array_slice($parts, -2));
                if (isset($whoisServers[$twoPartTld])) {
                    $tld = $twoPartTld;
                }
            }

            $server = $whoisServers[$tld] ?? 'whois.iana.org';
            $fp = @fsockopen($server, 43, $errno, $errstr, 4);

            if ($fp) {
                fputs($fp, $domain . "\r\n");
                $out = '';
                while (!feof($fp)) {
                    $out .= fgets($fp, 128);
                }
                fclose($fp);

                $patterns = [
                    '/Registry Expiry Date:\s*(.+)/i',
                    '/Expiration Date:\s*(.+)/i',
                    '/Expires On:\s*(.+)/i',
                    '/Expiry Date:\s*(.+)/i',
                    '/paid-till:\s*(.+)/i',
                    '/expire:\s*(.+)/i',
                    '/Expiration Time:\s*(.+)/i',
                ];

                foreach ($patterns as $pattern) {
                    if (preg_match($pattern, $out, $matches)) {
                        $dateStr = trim($matches[1]);
                        return Carbon::parse($dateStr);
                    }
                }
            }
        } catch (Exception $e) {
            // Ignore socket errors
        }

        return null;
    }

    /**
     * Format expiration data array.
     *
     * @param Carbon|null $expiresAt
     * @return array
     */
    protected function buildResult(?Carbon $expiresAt): array
    {
        if (!$expiresAt) {
            return [
                'expires_at' => null,
                'formatted'  => 'Tidak Terdeteksi',
                'days_left'  => null,
                'status'     => 'unknown',
                'human'      => 'Tidak Terdeteksi',
                'badge_class'=> 'badge-gray',
            ];
        }

        $now = Carbon::now();
        $daysLeft = (int) ceil($now->diffInDays($expiresAt, false));

        if ($expiresAt->isPast()) {
            $status = 'expired';
            $human = 'Expired';
            $badgeClass = 'badge-rose';
        } elseif ($daysLeft <= 30) {
            $status = 'warning';
            $human = "{$daysLeft} Hari Lagi (Kritis)";
            $badgeClass = 'badge-amber';
        } elseif ($daysLeft <= 60) {
            $status = 'warning';
            $human = "{$daysLeft} Hari Lagi";
            $badgeClass = 'badge-amber';
        } else {
            $status = 'valid';
            $human = "{$daysLeft} Hari Lagi";
            $badgeClass = 'badge-green';
        }

        return [
            'expires_at' => $expiresAt->toIso8601String(),
            'formatted'  => $expiresAt->format('d M Y'),
            'days_left'  => $daysLeft,
            'status'     => $status,
            'human'      => $human,
            'badge_class'=> $badgeClass,
        ];
    }
}
