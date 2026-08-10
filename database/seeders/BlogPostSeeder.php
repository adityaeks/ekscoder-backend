<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::first() ?? User::factory()->create([
            'name' => 'Aditya Ekscoder',
            'email' => 'admin@ekscoder.com',
        ]);

        $categories = [
            [
                'name' => 'Web Development',
                'slug' => 'web-development',
                'description' => 'Tutorial, best practices, dan tren seputar pengembangan website modern.',
                'color' => '#3b82f6',
            ],
            [
                'name' => 'DevOps & Cloud',
                'slug' => 'devops-cloud',
                'description' => 'Panduan Linux VPS, Docker, CI/CD pipeline, dan Cloud Architecture.',
                'color' => '#8b5cf6',
            ],
            [
                'name' => 'Tips & Productivity',
                'slug' => 'tips-productivity',
                'description' => 'Tips workflow, tools developer, dan efisiensi coding.',
                'color' => '#b8ff00',
            ],
        ];

        foreach ($categories as $catData) {
            $category = BlogCategory::firstOrCreate(['slug' => $catData['slug']], $catData);

            if ($catData['slug'] === 'web-development') {
                BlogPost::firstOrCreate(
                    ['slug' => 'panduan-lengkap-laravel-11-dan-vite'],
                    [
                        'title' => 'Panduan Lengkap Laravel 11 dan Vite untuk Web Modern',
                        'excerpt' => 'Pelajari cara mengintegrasikan Laravel 11 dengan Vite untuk mendapatkan performa build yang super cepat dan workflow modern.',
                        'content' => "## Pendahuluan\n\nLaravel 11 hadir dengan berbagai peningkatan signifikan dalam struktur direktori yang lebih ramping dan integrasi penuh dengan **Vite**.\n\n### Mengapa Menggunakan Vite?\n- **Instant Server Start**: Tidak perlu menunggu bundling saat development.\n- **Lightning Fast HMR**: Hot Module Replacement yang sangat responsif.\n- **Optimized Production Build**: Hasil bundling asset yang kecil dan efisien.\n\n### Kesimpulan\nIntegrasi ini mempermudah developer membangun aplikasi fullstack dengan pengalaman development terbaik.",
                        'cover_image' => 'https://images.unsplash.com/photo-1633356122544-f134324a6cee?auto=format&fit=crop&w=1200&q=80',
                        'category_id' => $category->id,
                        'author_id' => $adminUser->id,
                        'status' => 'published',
                        'featured' => true,
                        'views_count' => 142,
                        'meta_title' => 'Panduan Laravel 11 & Vite - Ekscoder',
                        'meta_description' => 'Tutorial integrasi Laravel 11 dan Vite untuk performa web modern.',
                        'meta_keywords' => 'laravel, vite, php, web development',
                        'published_at' => now()->subDays(2),
                    ]
                );

                BlogPost::firstOrCreate(
                    ['slug' => 'membangun-rest-api-high-performance-dengan-laravel'],
                    [
                        'title' => 'Membangun REST API High-Performance dengan Laravel & API Resources',
                        'excerpt' => 'Tips dan trik optimasi query Eloquent, caching response, dan format JSON standar menggunakan Laravel API Resources.',
                        'content' => "## Rest API di Laravel\n\nMerancang RESTful API yang cepat membutuhkan perhatian khusus pada query Eloquent agar terhindar dari N+1 problem.\n\n### Gunakan Eager Loading\nSelalu gunakan `with()` ketika meretrieve relasi data.\n\n### Caching Strategy\nGunakan Redis atau File Cache untuk data yang jarang berubah.",
                        'cover_image' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=1200&q=80',
                        'category_id' => $category->id,
                        'author_id' => $adminUser->id,
                        'status' => 'published',
                        'featured' => false,
                        'views_count' => 98,
                        'meta_title' => 'REST API High Performance Laravel',
                        'meta_description' => 'Cara membuat REST API cepat di Laravel.',
                        'meta_keywords' => 'api, laravel, rest, json',
                        'published_at' => now()->subDays(5),
                    ]
                );
            }

            if ($catData['slug'] === 'devops-cloud') {
                BlogPost::firstOrCreate(
                    ['slug' => 'cara-setup-vps-ubuntu-2404-untuk-laravel-production'],
                    [
                        'title' => 'Cara Setup VPS Ubuntu 24.04 LTS untuk Deployment Laravel Production',
                        'excerpt' => 'Langkah demi langkah mengonfigurasi Nginx, PHP-FPM 8.3, MySQL, Redis, dan SSL Certbot gratis di Linux VPS.',
                        'content' => "## Pengenalan Setup Server\n\nMenyiapkan VPS Linux sendiri memberikan fleksibilitas penuh serta kontrol performa yang optimal untuk aplikasi skala besar.\n\n### Langkah 1: Update Server & User Permission\nPastikan selalu memperbarui repositori sebelum menginstall dependensi.",
                        'cover_image' => 'https://images.unsplash.com/photo-1618401471353-b98afee0b2eb?auto=format&fit=crop&w=1200&q=80',
                        'category_id' => $category->id,
                        'author_id' => $adminUser->id,
                        'status' => 'published',
                        'featured' => true,
                        'views_count' => 310,
                        'meta_title' => 'Setup VPS Ubuntu 24.04 untuk Laravel',
                        'meta_description' => 'Tutorial deploy Laravel di VPS Ubuntu dengan Nginx dan PHP 8.3.',
                        'meta_keywords' => 'vps, ubuntu, nginx, deployment, laravel',
                        'published_at' => now()->subDays(1),
                    ]
                );
            }
        }
    }
}
