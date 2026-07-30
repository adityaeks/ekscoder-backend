<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CloudflareService;
use Illuminate\Http\Request;
use Exception;

class CloudflareZoneController extends Controller
{
    protected CloudflareService $cloudflare;

    public function __construct(CloudflareService $cloudflare)
    {
        $this->cloudflare = $cloudflare;
    }

    /**
     * Display list of Cloudflare Zones / Domains.
     */
    public function index(Request $request)
    {
        $zones = [];
        $error = null;
        $isConfigured = $this->cloudflare->isConfigured();

        if ($isConfigured) {
            try {
                $search = $request->query('search');
                $params = [];
                if ($search) {
                    $params['name'] = $search;
                }
                $response = $this->cloudflare->getZones($params);
                $zones = $response['result'] ?? [];
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        return view('admin.cloudflare.index', compact('zones', 'error', 'isConfigured'));
    }

    /**
     * Store / Create a new Zone (Add Domain).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]$/i'],
        ], [
            'name.required' => 'Nama domain wajib diisi.',
            'name.regex' => 'Format nama domain tidak valid (contoh: domainanda.com).',
        ]);

        try {
            $result = $this->cloudflare->createZone($request->input('name'));
            $zoneId = $result['result']['id'] ?? null;

            if ($zoneId) {
                return redirect()->route('admin.cloudflare-zones.show', $zoneId)
                    ->with('success', "Domain {$request->input('name')} berhasil ditambahkan ke Cloudflare!");
            }

            return redirect()->route('admin.cloudflare-zones.index')
                ->with('success', 'Domain berhasil ditambahkan!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display Zone detail page (DNS Records, Cache, Security, SSL).
     */
    public function show(string $zoneId)
    {
        try {
            $zoneResponse = $this->cloudflare->getZone($zoneId);
            $zone = $zoneResponse['result'] ?? null;

            if (!$zone) {
                return redirect()->route('admin.cloudflare-zones.index')->with('error', 'Zone tidak ditemukan.');
            }

            // Fetch DNS Records
            $dnsRecords = [];
            try {
                $dnsResponse = $this->cloudflare->getDnsRecords($zoneId);
                $dnsRecords = $dnsResponse['result'] ?? [];
            } catch (Exception $e) {
                // Log or report error
            }

            // Fetch SSL setting
            $sslSetting = 'full';
            try {
                $sslResponse = $this->cloudflare->getSslSetting($zoneId);
                $sslSetting = $sslResponse['result']['value'] ?? 'full';
            } catch (Exception $e) {
                // Ignore
            }

            // Fetch Security Level
            $securityLevel = 'medium';
            try {
                $secResponse = $this->cloudflare->getSecurityLevel($zoneId);
                $securityLevel = $secResponse['result']['value'] ?? 'medium';
            } catch (Exception $e) {
                // Ignore
            }

            return view('admin.cloudflare.show', compact('zone', 'dnsRecords', 'sslSetting', 'securityLevel'));
        } catch (Exception $e) {
            return redirect()->route('admin.cloudflare-zones.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Delete a Zone / Domain.
     */
    public function destroy(string $zoneId)
    {
        try {
            $this->cloudflare->deleteZone($zoneId);
            return redirect()->route('admin.cloudflare-zones.index')
                ->with('success', 'Domain/Zone berhasil dihapus dari Cloudflare.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Purge Cache for a Zone.
     */
    public function purgeCache(Request $request, string $zoneId)
    {
        $request->validate([
            'purge_type' => ['required', 'in:all,custom'],
            'urls' => ['required_if:purge_type,custom', 'nullable', 'string'],
        ]);

        try {
            $purgeEverything = $request->input('purge_type') === 'all';
            $files = [];

            if (!$purgeEverything && $request->filled('urls')) {
                $files = array_map('trim', explode("\n", $request->input('urls')));
            }

            $this->cloudflare->purgeCache($zoneId, $purgeEverything, $files);

            $msg = $purgeEverything ? 'Seluruh cache domain berhasil dibersihkan!' : 'Cache untuk URL terpilih berhasil dibersihkan!';
            return back()->with('success', $msg);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update SSL & Security level settings.
     */
    public function updateSecurity(Request $request, string $zoneId)
    {
        try {
            if ($request->has('ssl_mode')) {
                $this->cloudflare->updateSslSetting($zoneId, $request->input('ssl_mode'));
            }

            if ($request->has('security_level')) {
                $this->cloudflare->updateSecurityLevel($zoneId, $request->input('security_level'));
            }

            return back()->with('success', 'Pengaturan Keamanan & SSL berhasil diperbarui!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
