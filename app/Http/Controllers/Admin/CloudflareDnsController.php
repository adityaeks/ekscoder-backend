<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CloudflareService;
use Illuminate\Http\Request;
use Exception;

class CloudflareDnsController extends Controller
{
    protected CloudflareService $cloudflare;

    public function __construct(CloudflareService $cloudflare)
    {
        $this->cloudflare = $cloudflare;
    }

    /**
     * Store a new DNS record.
     */
    public function store(Request $request, string $zoneId)
    {
        $request->validate([
            'type' => ['required', 'string', 'in:A,AAAA,CNAME,TXT,MX,NS,SRV,LOC,SPF'],
            'name' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'ttl' => ['nullable', 'integer'],
            'proxied' => ['nullable', 'boolean'],
            'priority' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'comment' => ['nullable', 'string', 'max:100'],
        ], [
            'type.required' => 'Tipe DNS record wajib dipilih.',
            'name.required' => 'Nama hostname/subdomain wajib diisi.',
            'content.required' => 'IP Address / Nilai target wajib diisi.',
        ]);

        try {
            $data = $request->only(['type', 'name', 'content', 'ttl', 'priority', 'comment']);
            $data['proxied'] = $request->boolean('proxied');

            $this->cloudflare->createDnsRecord($zoneId, $data);

            return back()->with('success', "DNS Record {$data['type']} ({$data['name']}) berhasil dibuat!");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Update an existing DNS record.
     */
    public function update(Request $request, string $zoneId, string $recordId)
    {
        $request->validate([
            'type' => ['required', 'string', 'in:A,AAAA,CNAME,TXT,MX,NS,SRV,LOC,SPF'],
            'name' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'ttl' => ['nullable', 'integer'],
            'proxied' => ['nullable', 'boolean'],
            'priority' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'comment' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            $data = $request->only(['type', 'name', 'content', 'ttl', 'priority', 'comment']);
            $data['proxied'] = $request->boolean('proxied');

            $this->cloudflare->updateDnsRecord($zoneId, $recordId, $data);

            return back()->with('success', "DNS Record {$data['name']} berhasil diperbarui!");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Toggle Cloudflare Orange Cloud (Proxied Status).
     */
    public function toggleProxy(Request $request, string $zoneId, string $recordId)
    {
        try {
            $proxied = $request->boolean('proxied');
            $this->cloudflare->toggleProxyStatus($zoneId, $recordId, $proxied);

            $statusText = $proxied ? 'Proxied (Orange Cloud 🟠)' : 'DNS Only (Grey Cloud ⚪)';

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Status Proxy berhasil diubah menjadi: {$statusText}",
                    'proxied' => $proxied,
                ]);
            }

            return back()->with('success', "Status Proxy berhasil diubah menjadi {$statusText}!");
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete a DNS record.
     */
    public function destroy(string $zoneId, string $recordId)
    {
        try {
            $this->cloudflare->deleteDnsRecord($zoneId, $recordId);
            return back()->with('success', 'DNS Record berhasil dihapus dari Cloudflare.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
