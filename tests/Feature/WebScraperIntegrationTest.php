<?php

use App\Services\WebScraperService;
use Illuminate\Support\Facades\Http;

test('WebScraperService safely handles valid public URLs and cleans HTML', function () {
    Http::fake([
        'https://example.com/blog' => Http::response(
            '<html><head><title>Contoh Halaman Web</title><script>console.log("bad");</script></head><body><header><nav>Menu Navigasi</nav></header><main><h1>Judul Artikel</h1><p>Ini adalah konten utama artikel yang penting.</p></main><footer>Footer Copyright</footer></body></html>',
            200,
            ['Content-Type' => 'text/html; charset=UTF-8']
        ),
    ]);

    $service = new WebScraperService();
    $result = $service->browseUrl('https://example.com/blog');

    expect($result['success'])->toBeTrue();
    expect($result['title'])->toBe('Contoh Halaman Web');
    expect($result['content'])->toContain('Judul Artikel');
    expect($result['content'])->toContain('Ini adalah konten utama artikel yang penting.');
    // Script, nav, and footer should be stripped
    expect($result['content'])->not->toContain('console.log');
    expect($result['content'])->not->toContain('Menu Navigasi');
    expect($result['content'])->not->toContain('Footer Copyright');
});

test('WebScraperService handles JSON API responses gracefully', function () {
    Http::fake([
        'https://api.example.com/products' => Http::response(
            ['laptop' => 'Lenovo ThinkPad', 'price' => 15000000],
            200,
            ['Content-Type' => 'application/json']
        ),
    ]);

    $service = new WebScraperService();
    $result = $service->browseUrl('https://api.example.com/products');

    expect($result['success'])->toBeTrue();
    expect($result['content'])->toContain('Lenovo ThinkPad');
    expect($result['content'])->toContain('15000000');
});
