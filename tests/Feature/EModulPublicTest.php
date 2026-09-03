<?php

use App\Models\EModul;
use App\Services\NineRouterService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

test('public user can view active e-modul by slug without authentication', function () {
    $pdf = UploadedFile::fake()->create('sample.pdf', 500, 'application/pdf');
    $path = $pdf->store('emoduls', 'public');

    $modul = EModul::create([
        'title' => 'E-Modul Publik Pengujian',
        'slug' => 'e-modul-publik-pengujian',
        'category' => 'Umum',
        'file_path' => $path,
        'file_size' => 500000,
        'total_pages' => 5,
        'is_active' => true,
    ]);

    $response = $this->get(route('public.e-modul.show', $modul->slug));

    $response->assertStatus(200);
    $response->assertSee('E-Modul Publik Pengujian');
    $response->assertSee(route('public.e-modul.pdf', $modul->slug));
    $response->assertSee(route('public.e-modul.ask-ai', $modul->slug));
});

test('inactive e-modul returns 404 for public access', function () {
    $modul = EModul::create([
        'title' => 'E-Modul Non-Aktif',
        'slug' => 'e-modul-non-aktif',
        'category' => 'Umum',
        'file_path' => 'emoduls/fake.pdf',
        'file_size' => 1000,
        'total_pages' => 1,
        'is_active' => false,
    ]);

    $response = $this->get(route('public.e-modul.show', $modul->slug));

    $response->assertStatus(404);
});

test('public pdf stream returns pdf file with cors headers', function () {
    $pdf = UploadedFile::fake()->create('sample.pdf', 500, 'application/pdf');
    $path = $pdf->store('emoduls', 'public');

    $modul = EModul::create([
        'title' => 'E-Modul PDF Test',
        'slug' => 'e-modul-pdf-test',
        'category' => 'Umum',
        'file_path' => $path,
        'file_size' => 500000,
        'total_pages' => 3,
        'is_active' => true,
    ]);

    $response = $this->get(route('public.e-modul.pdf', $modul->slug));

    $response->assertStatus(200);
    $response->assertHeader('Access-Control-Allow-Origin', '*');
});

test('public ask-ai endpoint processes question and handles response', function () {
    $modul = EModul::create([
        'title' => 'E-Modul AI Test',
        'slug' => 'e-modul-ai-test',
        'category' => 'Umum',
        'file_path' => 'emoduls/fake.pdf',
        'file_size' => 1000,
        'total_pages' => 2,
        'is_active' => true,
    ]);

    // Mock NineRouterService
    $mockAi = Mockery::mock(NineRouterService::class);
    $mockAi->shouldReceive('getChatCompletions')
        ->once()
        ->andReturn('Jawaban AI untuk modul pengujian.');
    $this->app->instance(NineRouterService::class, $mockAi);

    $response = $this->postJson(route('public.e-modul.ask-ai', $modul->slug), [
        'question' => 'Apa isi materi bab 1?',
        'page_number' => 'Halaman 1',
        'page_text' => 'Ini adalah materi bab 1 tentang pengantar.',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'reply' => 'Jawaban AI untuk modul pengujian.',
    ]);
});
