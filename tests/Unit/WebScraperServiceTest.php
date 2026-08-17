<?php

use App\Services\WebScraperService;

test('WebScraperService blocks localhost for SSRF protection', function () {
    $service = new WebScraperService();
    $result = $service->browseUrl('http://localhost:8000/test');

    expect($result['success'])->toBeFalse();
    expect($result['error'])->toContain('Akses URL diblokir demi keamanan');
});

test('WebScraperService extracts URLs correctly', function () {
    $service = new WebScraperService();
    $text = 'Tolong buka https://example.com/item/1 dan juga http://google.com!';
    $urls = $service->extractUrls($text);

    expect($urls)->toEqual(['https://example.com/item/1', 'http://google.com']);
});
