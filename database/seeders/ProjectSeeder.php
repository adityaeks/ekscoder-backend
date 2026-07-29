<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            [
                'id' => 'erp-system',
                'number' => '01',
                'title' => 'ERP SYSTEM',
                'category' => 'Enterprise Software',
                'year' => '2026',
                'description' => 'High-performance enterprise resource planning platform featuring real-time analytics, automated inventory tracking, and seamless financial workflow automation.',
                'technologies' => ['Next.js', 'TypeScript', 'Node.js', 'PostgreSQL', 'Docker'],
                'image_bg' => 'from-emerald-900/40 via-neutral-900 to-black',
                'accent_color' => '#10B981',
                'link' => 'https://example.com',
                'featured' => true,
                'is_active' => true,
                'order' => 1,
            ],
            [
                'id' => 'greenoryyinn',
                'number' => '02',
                'title' => 'GREENORYYINN',
                'category' => 'Hospitality Platform',
                'year' => '2025',
                'description' => 'Boutique eco-resort booking experience with ultra-smooth motion transitions, interactive 3D room preview, and integrated booking engine.',
                'technologies' => ['Next.js', 'Tailwind CSS', 'GSAP', 'Stripe API'],
                'image_bg' => 'from-lime-900/40 via-neutral-900 to-black',
                'accent_color' => '#B8FF00',
                'link' => 'https://example.com',
                'featured' => true,
                'is_active' => true,
                'order' => 2,
            ],
            [
                'id' => 'vps-control',
                'number' => '03',
                'title' => 'VPS CONTROL',
                'category' => 'Infrastructure Management',
                'year' => '2026',
                'description' => 'Cloud infrastructure dashboard providing server metrics monitoring, automated deployments, firewall rule management, and terminal session streaming.',
                'technologies' => ['React', 'Go', 'Docker', 'Nginx', 'WebSockets'],
                'image_bg' => 'from-cyan-900/40 via-neutral-900 to-black',
                'accent_color' => '#06B6D4',
                'link' => 'https://example.com',
                'featured' => true,
                'is_active' => true,
                'order' => 3,
            ],
            [
                'id' => 'hyperflow',
                'number' => '04',
                'title' => 'HYPERFLOW',
                'category' => 'SaaS & Workflow Engine',
                'year' => '2025',
                'description' => 'Visual node-based automation platform built for digital agencies to orchestrate APIs, webhooks, and AI pipelines seamlessly.',
                'technologies' => ['TypeScript', 'Next.js', 'Redis', 'Tailwind CSS'],
                'image_bg' => 'from-purple-900/40 via-neutral-900 to-black',
                'accent_color' => '#A855F7',
                'link' => 'https://example.com',
                'featured' => false,
                'is_active' => true,
                'order' => 4,
            ],
            [
                'id' => 'nova-creative',
                'number' => '05',
                'title' => 'NOVA CREATIVE',
                'category' => 'Interactive Agency Web',
                'year' => '2025',
                'description' => 'Award-winning immersive web showcase with WebGL particle shaders, smooth scroll progress, and custom cursor interaction design.',
                'technologies' => ['Three.js', 'GSAP', 'ScrollTrigger', 'WebGL'],
                'image_bg' => 'from-rose-900/40 via-neutral-900 to-black',
                'accent_color' => '#F43F5E',
                'link' => 'https://example.com',
                'featured' => false,
                'is_active' => true,
                'order' => 5,
            ],
            [
                'id' => 'cybernexus',
                'number' => '06',
                'title' => 'CYBERNEXUS',
                'category' => 'AI Cloud Monitoring',
                'year' => '2026',
                'description' => 'Intelligent telemetry aggregator using machine learning models to detect cloud infrastructure anomalies and predict resource demand.',
                'technologies' => ['Next.js', 'Python', 'GraphQL', 'Tailwind CSS'],
                'image_bg' => 'from-amber-900/40 via-neutral-900 to-black',
                'accent_color' => '#F59E0B',
                'link' => 'https://example.com',
                'featured' => false,
                'is_active' => true,
                'order' => 6,
            ],
        ];

        foreach ($projects as $projectData) {
            Project::updateOrCreate(['id' => $projectData['id']], $projectData);
        }
    }
}
